<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Extended Security Class
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class MySecurity extends \Fuel\Core\Security
{

	public static function htmlspecialchars($value)
	{
		static $already_cleaned = array();

		// Nothing to escape for non-string scalars, or for already processed values
		if (is_bool($value) or is_int($value) or is_float($value) or in_array($value, $already_cleaned, true))
		{
			return $value;
		}

		if (is_string($value))
		{
			// Caution: I changed 2nd and 4th parameters from fuel's default htmlentities()
			$value = htmlspecialchars($value, ENT_QUOTES, \Fuel::$encoding);
		}
		elseif (is_array($value) or ($value instanceof \Iterator and $value instanceof \ArrayAccess))
		{
			// Add to $already_cleaned variable when object
			is_object($value) and $already_cleaned[] = $value;

			foreach ($value as $k => $v)
			{
				$value[$k] = static::htmlspecialchars($v);
			}
		}
		elseif ($value instanceof \Iterator or get_class($value) == 'stdClass')
		{
			// Add to $already_cleaned variable
			$already_cleaned[] = $value;

			foreach ($value as $k => $v)
			{
				$value->{$k} = static::htmlspecialchars($v);
			}
		}
		elseif (is_object($value))
		{
			// Check if the object is whitelisted and return when that's the case
			foreach (\Config::get('security.whitelisted_classes', array()) as $class)
			{
				if (is_a($value, $class))
				{
					// Add to $already_cleaned variable
					$already_cleaned[] = $value;

					return $value;
				}
			}

			// Throw exception when it wasn't whitelisted and can't be converted to String
			if ( ! method_exists($value, '__toString'))
			{
				throw new \RuntimeException('Object class "'.get_class($value).'" could not be converted to string or '.
					'sanitized as ArrayAcces. Whitelist it in security.whitelisted_classes in app/config/config.php '.
					'to allow it to be passed unchecked.');
			}

			$value = static::htmlspecialchars((string) $value);
		}

		return $value;
	}
	
	public static function check_encoding($value)
	{
		if (is_array($value))
		{
			array_map(array('Security', 'check_encoding'), $value);
			return $value;
		}
		
		if (mb_check_encoding($value, \Fuel::$encoding)) {
			return $value;
		}
		else
		{
			\Log::error(
				'Invalid charactor encoding: '.
				\Input::uri().' '.
				urlencode($value).' '.
				\Input::ip().
				' "'.\Input::user_agent().'"'
			);
			throw new HttpInvalidInputException('Invalid input data');
		}
	}
	
	public static function check_controll($value)
	{
		if (is_array($value))
		{
			array_map(array('Security', 'check_controll'), $value);
			return $value;
		}
		
		if (preg_match('/\A[\r\n\t[:^cntrl:]]*\z/u', $value) === 1)
		{
			return $value;
		}
		else
		{
			\Log::error(
				'Invalid controll charactors: '.
				\Input::uri().' '.
				urlencode($value).' '.
				\Input::ip().
				' "'.\Input::user_agent().'"'
			);
			throw new HttpInvalidInputException('Invalid input data');
		}
	}
}
