<?php
/**
* Sample Contact Form
*
* @author     Kenji Suzuki https://github.com/kenjis
* @copyright  2011-2012 Kenji Suzuki
* @license    MIT License http://www.opensource.org/licenses/mit-license.php
*/

/**
 * My Input Filters
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class MyInputFilters
{
	/**
	* Checks charset encoding
	*
	* @param string|array $value
	* @return string|Exception
	*/
	public static function check_encoding($value)
	{
		if (is_array($value))
		{
			array_map(array('MyInputFilters', 'check_encoding'), $value);
			return $value;
		}
		
		if (mb_check_encoding($value, Fuel::$encoding))
		{
			return $value;
		}
		else
		{
			static::log_error('Invalid character encoding', $value);
			throw new HttpInvalidInputException('Invalid input data');
		}
	}
	
	/**
	* Checks control code
	*
	* @param string|array $value
	* @return string|Exception
	*/
	public static function check_control($value)
	{
		if (is_array($value))
		{
			array_map(array('MyInputFilters', 'check_control'), $value);
			return $value;
		}
		
		if (preg_match('/\A[\r\n\t[:^cntrl:]]*\z/u', $value) === 1)
		{
			return $value;
		}
		else
		{
			static::log_error('Invalid control characters', $value);
			throw new HttpInvalidInputException('Invalid input data');
		}
	}
	
	/**
	* Standardize Newline with \n
	*
	* @param string|array $value
	* @return string
	*/
	public static function standardize_newline($value)
	{
		if (is_array($value))
		{
			array_map(array('MyInputFilters', 'standardize_newline'), $value);
			return $value;
		}
	
		if (strpos($value, "\r") !== false)
		{
			$value = str_replace(array("\r\n", "\r"), "\n", $value);
		}
	
		return $value;
	}
	
	public static function log_error($msg, $value)
	{
		Log::error(
			$msg . ': ' . Input::uri() . ' ' . urlencode($value) . ' ' .
			Input::ip() . ' "' . Input::user_agent() . '"'
		);
	}
}
