<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Extended Form Class
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Form extends \Fuel\Core\Form
{
	/**
	 * Prep Value
	 *
	 * Prepares the value for display in the form
	 *
	 * @param   string
	 * @return  string
	 */
	public static function prep_value($value)
	{
		$value = htmlspecialchars($value, ENT_QUOTES, \Fuel::$encoding);

		return $value;
	}
}
