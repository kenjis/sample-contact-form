<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Controller Form class Functional Tests
 * 
 * @group Functional
 */
class Tests_Controller_Form_ extends \TestCase
{
	const BASE_URL = 'http://localhost/form/form/';
	protected static $curl;

	public static function setUpBeforeClass()
	{
		self::$curl = new Curl();
	}
 
	public static function tearDownAfterClass()
	{
		self::$curl = null;
	}

	public function test_action_index()
	{
		$url = self::BASE_URL;
		self::$curl->set_url($url);
		$output = self::$curl->get();
		
		$this->assertRegExp('/コンタクトフォーム/u', $output);
	}

	public function test_action_confirm_error_no_email()
	{
		$post = array(
			'name'    => '<s>name</s>"&\'',
			'email'   => '',
			'comment' => '<s>comment</s>"&\''
		);

		$url = self::BASE_URL . 'confirm';
		self::$curl->set_url($url);
		self::$curl->set_post_data($post);
		$output = self::$curl->post();

		$this->assertRegExp('!<input type="text" required="required" value="&lt;s&gt;name&lt;/s&gt;&quot;&amp;&#39;" id="name" name="name" />!u', $output);
		$this->assertRegExp('!<textarea cols="70" rows="6" required="required" id="comment" name="comment">&lt;s&gt;comment&lt;/s&gt;&quot;&amp;&#39;</textarea>!u', $output);
		$this->assertRegExp('/メールアドレス 欄は必須です。/u', $output);
	}
	
	public function test_action_confirm_error_mail_header_injection()
	{
		$post = array(
			'name'    => '<s>name</s>"&\'',
			'email'   => "username@example.jp\nBcc: foo@example.com",
			'comment' => '<s>comment</s>"&\'',
		);

		$url = self::BASE_URL . 'confirm';
		self::$curl->set_url($url);
		self::$curl->set_post_data($post);
		$output = self::$curl->post();

		$this->assertRegExp('!Invalid input data!u', $output);
	}
	
	public function test_action_confirm_passed()
	{
		$post = array(
			'name'    => '<s>name</s>"&\'',
			'email'   => 'username@example.jp',
			'comment' => '<s>comment</s>"&\'',
		);
		
		$url = self::BASE_URL . 'confirm';
		self::$curl->set_url($url);
		self::$curl->set_post_data($post);
		self::$curl->set_token_regex('/name="fuel_csrf_token" value="([0-9a-z]+)"/u');
		$output = self::$curl->post();
		
		$this->assertRegExp('!&lt;s&gt;name&lt;/s&gt;&quot;&amp;&#039;!u', $output);
		$this->assertRegExp('!&lt;s&gt;comment&lt;/s&gt;&quot;&amp;&#039;!u', $output);
		$this->assertNotRegExp('/メールアドレス 欄は必須です。/u', $output);
	}
	
	public function test_action_send_passed()
	{
		$post = array(
			'name'    => 'functional test',
			'email'   => 'username@example.jp',
			'comment' => 'Testing of Form controller',
			'fuel_csrf_token' => self::$curl->get_token(),
		);
		
		$url = self::BASE_URL . 'send';
		self::$curl->set_url($url);
		self::$curl->set_post_data($post);
		$output = self::$curl->post();
		
		$this->assertRegExp('/コンタクトフォーム: 送信完了/u', $output);
	}
}

/* End of file app/tests/controller/form_.php */