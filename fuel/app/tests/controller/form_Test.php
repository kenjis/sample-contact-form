<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

// override internal mail() function
namespace Email
{
	function mail($to, $subject, $message, $additional_headers, $additional_parameters)
	{
		$data = array(
			'to' => $to,
			'subject' => $subject,
			'message' => $message,
			'additional_headers' => $additional_headers,
			'additional_parameters' => $additional_parameters,
		);
		
		\Tests_Controller_Form::$mail_data = $data;
		
		return true;
	}
}

namespace {
	/**
	 * Controller Form class Tests
	 * 
	 * @group App
	 */
	class Tests_Controller_Form extends \TestCase
	{
		public static $mail_data; // data passed to mail() function
		
		public function test_sendmail()
		{
			$c = new Controller_Form(new \Request('form/send'), new \Response);

			$data['from']      = 'foo@example.jp';
			$data['from_name'] = '送信者の名前';
			$data['to']        = 'kenji.uui@gmail.com';
			$data['to_name']   = '管理者';
			$data['subject']   = 'コンタクトフォーム';
			
			$ip           = \Input::ip();
			$agent        = \Input::user_agent();
			$data['body'] = <<< END
====================
名前: {$data['from_name']}
メールアドレス: {$data['from']}
IPアドレス: $ip
ブラウザ: $agent
====================
コメント: 
sendmail()メソッドのテスト
====================
END;

			$c->sendmail($data);
			
			$this->assertRegExp('/管理者 <kenji.uui@gmail.com>/u', self::$mail_data['to']);
			$this->assertRegExp('/コンタクトフォーム/u', self::$mail_data['subject']);
			$this->assertRegExp('/sendmail\(\)メソッドのテスト/u', self::$mail_data['message']);
			$this->assertRegExp('/From: 送信者の名前 <foo@example.jp>/u', self::$mail_data['additional_headers']);
			$this->assertRegExp('/-oi -f foo@example.jp/u', self::$mail_data['additional_parameters']);
		}
	}
}

/* End of file app/tests/controller/form.php */