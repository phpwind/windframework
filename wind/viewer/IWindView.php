<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindView {

	/**
	 * 视图渲染
	 * 
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 */
	public function render();

}

?>