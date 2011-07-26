<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindFrontController {

	/**
	 * @return WindsystemConfig
	 */
	public function getWindSystemConfig();

	/**
	 * @return WindComponentFactory
	 */
	public function getWindFactory();

	/**
	 * @return WindHttpRequest
	 */
	public function getRequest();

	/**
	 * @return WindHttpResponse
	 */
	public function getResponse();

}

?>