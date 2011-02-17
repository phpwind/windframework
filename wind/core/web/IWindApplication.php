<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindApplication {

	/**
	 * 请求处理
	 */
	public function processRequest();

	/**
	 * 请求转发
	 * 
	 * @param WindForward $forward
	 */
	public function doDispatch($forward);

}

?>