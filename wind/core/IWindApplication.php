<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindApplication {

	/**
	 * Enter description here ...
	 * @return
	 */
	public function run();

	/**
	 * 请求转发
	 * @param WindForward $forward
	 */
	public function doDispatch($forward);

	/**
	 * @return WindHttpRequest $request
	 */
	public function getRequest();

	/**
	 * @return WindHttpResponse $response
	 */
	public function getResponse();

	/**
	 * @return WindSystemConfig $windSystemConfig
	 */
	public function getWindSystemConfig();

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory();
}
?>