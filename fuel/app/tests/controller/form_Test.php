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
			
			$test = static::$mail_data['to'];
			$pattern = '/' . preg_quote('=?UTF-8?B?566h55CG6ICF?= <kenji.uui@gmail.com>', '/') . '/u';
			$this->assertRegExp($pattern, $test);
			
			$test = static::$mail_data['subject'];
			$pattern = '/' . preg_quote('=?UTF-8?B?44Kz44Oz44K/44Kv44OI44OV44Kp44O844Og?=', '/') . '/u';
			$this->assertRegExp($pattern, $test);
			
			$test = static::$mail_data['message'];
			$pattern = '/' . preg_quote('sendmail()メソッドのテスト', '/') . '/u';
			$this->assertRegExp($pattern, $test);
			
			$test = static::$mail_data['additional_headers'];
			$pattern = '/' . preg_quote('From: =?UTF-8?B?6YCB5L+h6ICF44Gu5ZCN5YmN?= <foo@example.jp>', '/') . '/u';
			$this->assertRegExp($pattern, $test);
			
			$test = static::$mail_data['additional_parameters'];
			$pattern = '/' . preg_quote('-oi -f foo@example.jp', '/') . '/u';
			$this->assertRegExp($pattern, $test);
		}
	}
}

/* End of file app/tests/controller/form.php */