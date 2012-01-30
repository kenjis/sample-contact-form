<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Curl Class
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Curl
{
	protected $ch;
	protected $cookiejar;
	protected $cookifile;
	protected $result;
	protected $token_regex; // regular expression for csrf token
	protected $token;       // csrf token
	
	public function __construct()
	{
		$this->cookiejar  = APPPATH . 'tmp/cookiejar';
		$this->cookiefile = APPPATH . 'tmp/cookiefile';
		
		$this->ch = curl_init();
		
		// return the result on success, FALSE on failure
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookiejar);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
		
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Curl HTTP Client');
		
		return $this;
	}
	
	public function __destruct()
	{
		curl_close($this->ch);
	}
	
	public function set_url($url)
	{
		curl_setopt($this->ch, CURLOPT_URL, $url);
		return $this;
	}
	
	public function get()
	{
		curl_setopt($this->ch, CURLOPT_POST, false);
		$this->result = curl_exec($this->ch);
		
		if ($this->token_regex)
		{
			$this->set_token();
			$this->token_regex = null;
		}
		
		return $this->result;
	}
	
	public function set_post_data($post)
	{
		$post = http_build_query($post);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
		return $this;
	}
	
	public function post()
	{
		curl_setopt($this->ch, CURLOPT_POST, true);
		$this->result = curl_exec($this->ch);
		
		if ($this->token_regex)
		{
			$this->set_token();
			$this->token_regex = null;
		}
		
		return $this->result;
	}
	
	public function get_status()
	{
		return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	}
	
	public function set_token_regex($regex)
	{
		$this->token_regex = $regex;
		return $this;
	}
	
	public function get_token()
	{
		return $this->token;
	}
	
	protected function set_token()
	{
		preg_match($this->token_regex, $this->result, $matches);
		if ( ! empty($matches[1]))
		{
			$this->token = $matches[1];
		}
		else
		{
			throw new Exception("can't get token");
		}
	}
}
