<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindController {

	/**
	 * 处理请求并返回Forward对象
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return WindForward
	 */
	public function doAction($handlerAdapter);

	/**
	 * Action预处理方法
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return
	 */
	public function preAction($handlerAdapter);

	/**
	 * Action后处理方法
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return
	 */
	public function postAction($handlerAdapter);
}
?>