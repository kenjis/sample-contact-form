<?php
/**
* Sample Contact Form
*
* @author     Kenji Suzuki https://github.com/kenjis
* @copyright  2011-2012 Kenji Suzuki
* @license    MIT License http://www.opensource.org/licenses/mit-license.php
*/

/**
 * My Validation Rules
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class MyValidationRules
{
	/**
	* Validate select, radio, checkbox data
	*
	* @param   string|array
	* @param   array  valid options
	* @return  true|Exception
	*/
	public static function _validation_in_array($val, $compare)
	{
		if (Validation::_empty($val))
		{
			return true;
		}
	
		if ( ! is_array($val))
		{
			$val = array($val);
		}
	
		foreach ($val as $value)
		{
			if ( ! in_array($value, $compare))
			{
				throw new HttpInvalidInputException('Invalid input data');
			}
		}
	
		return true;
	}
	
	/**
	 * Validate not required array input
	 *
	 * @param   array
	 * @return  true|array
	 */
	public static function _validation_not_required_array($val)
	{
		if (is_array($val))
		{
			return true;
		}
		else
		{
			return array();
		}
	}
	
	/**
	* Checks not including newlines or tabs
	*
	* @param   string
	* @return  bool
	*/
	public static function _validation_no_tab_and_newline($value)
	{
		if (preg_match('/\A[^\r\n\t]*\z/u', $value) === 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
