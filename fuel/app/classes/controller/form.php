<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Form Controller
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Form extends Controller_Template
{

	public function before()
	{
		parent::before();
		$this->response = Response::forge();
		$this->response->set_header('X-FRAME-OPTIONS', 'SAMEORIGIN');
	}
	
	public function after($response)
	{
		$response = $this->response;
		$response->body = $this->template;
		return parent::after($response);
	}
	
	public function form()
	{
		$form = Fieldset::forge();

		$form->add('name', '名前')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('no_tab_and_newline')
			->add_rule('max_length', 20);

		$form->add('email', 'メールアドレス')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('no_tab_and_newline')
			->add_rule('valid_email');

		$form->add('comment', 'コメント', 
			array('type' => 'textarea', 'cols' => 70, 'rows' => 6))
			->add_rule('required')
			->add_rule('max_length', 400);

		$ops = array(
			'男性' => '男性', 
			'女性' => '女性',
		);
		$form->add('gender', '性別', 
			array('options' => $ops, 'type' => 'radio'))
			->add_rule('in_array', $ops);
		
		$ops = array(
			''                         => '',
			'製品購入前のお問い合わせ' => '製品購入前のお問い合わせ', 
			'製品購入後のお問い合わせ' => '製品購入後のお問い合わせ', 
			'その他'                   => 'その他',
		);
		$form->add('kind', '問い合わせの種類', 
			array('options' => $ops, 'type' => 'select'))
			->add_rule('in_array', $ops);
		
		$ops = array(
			'PHP'    => 'PHP', 
			'Perl'   => 'Perl', 
			'Python' => 'Python',
		);
		$form->add('lang', '使用プログラミング言語', 
			array('options' => $ops, 'type' => 'checkbox'))
			->add_rule('in_array', $ops)
			->add_rule('not_required_array');
		
		$form->add('submit', '', array('type'=>'submit', 'value' => '確認'));
		
		return $form;
	}
	
	public function action_index()
	{
		$form = $this->form();
		
		if (Input::method() === 'POST')
		{
			$form->repopulate();
		}
		
		$this->template->title = 'コンタクトフォーム';
		$this->template->content = View::forge('form/index');
		$this->template->content->set_safe('html_form', $form->build('form/confirm'));
	}
	
	public function action_confirm()
	{
		$form = $this->form();
		$val  = $form->validation();
		$val->add_callable('myvalidationrules');
		
		if ($val->run())
		{
			$data['input'] = $val->validated();
			$this->template->title = 'コンタクトフォーム: 確認';
			$this->template->content = View::forge('form/confirm', $data);
		}
		else
		{
			$form->repopulate();
			
			$this->template->title = 'コンタクトフォーム: エラー';
			$this->template->content = View::forge('form/index');
			$this->template->content->set_safe('html_error', $val->show_errors());
			$this->template->content->set_safe('html_form', $form->build('form/confirm'));
		}
	}

	public function action_send()
	{
		if ( ! \Security::check_token())
		{
			\Log::error(
				'CSRF: '.
				\Input::uri().' '.
				\Input::ip().
				' "'.\Input::user_agent().'"'
			);
			throw new HttpInvalidInputException('Invalid input data');
		}
		
		$val = $this->form()->validation();
		$val->add_callable('myvalidationrules');
		
		if ($val->run())
		{
			$post = $val->validated();
			
			\Config::load('contact_form', true);
			//Debug::dump(\Config::get('contact_form'));
			
			$data['from']      = $post['email'];
			$data['from_name'] = $post['name'];
			$data['to']        = \Config::get('contact_form.admin_email');
			$data['to_name']   = \Config::get('contact_form.admin_name');
			$data['subject']   = \Config::get('contact_form.mail_subject');
			
			$ip           = \Input::ip();
			$agent        = \Input::user_agent();
			$langs = implode(' ', $post['lang']);
			
			$data['body'] = <<< END
====================
名前: {$post['name']}
メールアドレス: {$post['email']}
IPアドレス: $ip
ブラウザ: $agent
====================
コメント: 
{$post['comment']}

性別: {$post['gender']}
問い合わせの種類: {$post['kind']}
使用プログラミング言語: $langs
====================
END;
			
			try
			{
				$this->sendmail($data);
				$this->template->title = 'コンタクトフォーム: 送信完了';
				$this->template->content = View::forge('form/send');
			}
			catch(EmailValidationFailedException $e)
			{
				$this->template->title = 'コンタクトフォーム: 送信エラー';
				$this->template->content = View::forge('form/error');
				
				\Log::error(
					__METHOD__ . ' email validation error: ' .
					$e->getMessage()
				);
			}
			catch(EmailSendingFailedException $e)
			{
				$this->template->title = 'コンタクトフォーム: 送信エラー';
				$this->template->content = View::forge('form/error');
				
				\Log::error(
					__METHOD__ . ' email sending error: ' .
					$e->getMessage()
				);
			}
		}
		else
		{
			$this->template->title = 'コンタクトフォーム: エラー';
			$this->template->content = View::forge('form/index');
			$this->template->content->set_safe('html_error', $val->show_errors());
		}
	}
	
	public function sendmail($data)
	{
		Package::load('email');
		
		$items = array('from', 'from_name', 'to', 'to_name', 'subject');
		foreach ($items as $item)
		{
			if (preg_match('/[\r\n]/u', $data[$item]) === 1)
			{
				throw new EmailValidationFailedException('One or more email headers did not pass validation: '.$item);
			}
		}
		
		$email = Email::forge();
		$email->from($data['from'], $data['from_name']);
		$email->to($data['to'], $data['to_name']);
		$email->subject($data['subject']);
		$email->body($data['body']);
		
		$email->send();
	}
}
