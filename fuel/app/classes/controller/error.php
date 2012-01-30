<?php

/**
 * Sample Contact Form
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */

/**
 * Error Controller
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2011 Kenji Suzuki
 * @license    MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Error extends Controller_Template
{
	public function before()
	{
		parent::before();
		$this->response->set_header('X-FRAME-OPTIONS', 'SAMEORIGIN');
	}
	
	/**
	 * The 404 action for the application.
	 * 
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		$this->template->title = '404 Not Found';
		$this->template->content = View::forge('error/404');
		$this->response->status = 404;
	}
	
	/**
	 * Error page for invalid input data
	 */
	public function action_invalid()
	{
		$this->template->title = 'Invalid input data';
		$this->template->content = View::forge('error/invalid');
	}
}
