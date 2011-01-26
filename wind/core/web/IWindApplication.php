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
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function processRequest($request, $response);

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function doDispatch($request, $response);
	
}

?>