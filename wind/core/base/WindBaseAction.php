<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WindBaseAction {
	/**
	 * 页面跳转信息model and view对象
	 * 
	 * @var $mav
	 */
	protected $mav = null;
	
	/**
	 * 输出参数信息
	 * 
	 * @var $viewer
	 */
	protected $view = null;
	
	public function __construct() {
		$this->view = new stdClass();
		$this->mav = new WindModelAndView();
	}
	
	public function beforeAction() {}
	
	public function afterAction() {}
	
	/**
	 * 返回视图对像
	 * 
	 * @param WRouter $router
	 * @return WindForward
	 */
	public function getModulAndView() {
		return $this->mav;
	}

}