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
		$this->response->set_header('X-FRAME-OPTIONS', 'SAMEORIGIN');
	}
	
	public function validate()
	{
		$val = Validation::forge();

		$val->add('name', '名前')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('no_controll')
			->add_rule('max_length', 20);

		$val->add('email', 'メールアドレス')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('no_controll')
			->add_rule('valid_email');

		$val->add('comment', 'コメント')
			->add_rule('required')
			->add_rule('max_length', 400);

		$val->add('gender', '性別')
			->add_rule('in_array', array('男性', '女性'));
		
		$val->add('kind', '問い合わせの種類')
			->add_rule('in_array', array('製品購入前のお問い合わせ', '製品購入後のお問い合わせ', 'その他'));
		
		$val->add('lang', '使用プログラミング言語')
			->add_rule('in_array', array('PHP', 'Perl', 'Python'))
			->add_rule('not_required_array');
		
		return $val;
	}
	
	public function action_index()
	{
		$this->template->title = 'コンタクトフォーム';
		$this->template->content = View::forge('form/index');
	}
	
	
	public function action_confirm()
	{
		$val = $this->validate();
		
		if ($val->run())
		{
			$data['input'] = $val->validated();
			$this->template->title = 'コンタクトフォーム: 確認';
			$this->template->content = View::forge('form/confirm', $data);
		}
		else
		{
			$this->template->title = 'コンタクトフォーム: エラー';
			$this->template->content = View::forge('form/index');
			$this->template->content->set_safe('html_error', $val->show_errors());
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

		$val = $this->validate();
		
		if ($val->run())
		{
			$post = $val->validated();
			
			\Config::load('form', true);
			
			$data['from']      = $post['email'];
			$data['from_name'] = $post['name'];
			$data['to']        = \Config::get('form.admin_email');
			$data['to_name']   = \Config::get('form.admin_name');
			$data['subject']   = \Config::get('form.mail_subject');
			
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
			}
			catch(EmailSendingFailedException $e)
			{
				$this->template->title = 'コンタクトフォーム: 送信エラー';
				$this->template->content = View::forge('form/error');
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
