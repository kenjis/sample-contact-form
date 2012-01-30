<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * HttpInvalidInputException class Tests
 * 
 * @group App
 */
class Tests_HttpInvalidInputException extends \TestCase
{
	/**
	* @expectedException HttpInvalidInputException
	* @expectedExceptionMessage Invalid input data
	*/
	public function test_exception()
	{
		throw new \HttpInvalidInputException('Invalid input data');
	}
}

/* End of file app/tests/httpinvalidinputexception_Test.php */