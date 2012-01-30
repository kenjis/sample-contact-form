<?php
/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * HttpInvalidInputException Class
 *
 * This Exception is for invalid input data. Shutdown application.
 * 
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class HttpInvalidInputException extends \HttpException
{
	/**
	 * return a response object for the handle method
	 */
	public function response()
	{
		$response = Request::forge('error/invalid')->execute()->response();
		
		// This will add the execution time and memory usage to the output.
		// Comment this out if you don't use it.
		$bm = Profiler::app_total();
		$response->body(
			str_replace(
				array('{exec_time}', '{mem_usage}'),
				array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)),
				$response->body()
			)
		);
		
		return $response;
	}
}
