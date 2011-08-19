<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindApplication {

	/**
	 * @return
	 */
	public function run();

	/**
	 * @return WindHttpRequest $request
	 */
	public function getRequest();

	/**
	 * @return WindHttpResponse $response
	 */
	public function getResponse();

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory();
}
?>