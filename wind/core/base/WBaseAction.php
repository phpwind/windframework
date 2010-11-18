<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WBaseAction {
	/**
	 * 视图信息
	 * 
	 * @var $forward
	 */
	protected $forward = '';
	
	/**
	 * 页面布局信息
	 * 
	 * @var $layout
	 */
	protected $layout = '';
	
	/**
	 * 输出参数信息
	 * 
	 * @var $viewer
	 */
	protected $view = null;
	
	public function __construct() {
		$this->view = new stdClass();
		$this->layout = new WLayout();
	}
	
	public function beforeAction() {}
	
	public function afterAction() {}
	
	/**
	 * 设置视图跳转信息
	 * 
	 * @param string $forward
	 */
	protected function setForward($forward) {
		$this->forward = $forward;
	}
	
	/**
	 * 设置视图变量
	 * 
	 * @param  $view
	 */
	protected function setView($key, $value = '') {
		if (is_string($key)) {
			($value == '') ? $this->view->default = $key : $this->view->$key = $value;
		} elseif (is_object($key)) {
			$value = get_object_vars($key);
			foreach ($value as $k => $v) {
				($k) && $this->view->$k = $v;
			}
		}
		return;
	}
	
	/**
	 * 设置页面布局切片模板
	 * 
	 * @param string|array $segment
	 */
	protected function setSegment($segment) {
		if ($this->layout == null)
			return;
		$this->layout->setSegments($segment);
	}
	
	/**
	 * 设置视图的布局信息
	 * 
	 * @param string $layout
	 */
	protected function setLayout($layout) {
		$this->layout->setLayout($layout);
	}
	
	/**
	 * 返回视图对像
	 * @param WhttpRequest $request
	 * @param WHttpResponse $response
	 * @param WRouter $router
	 */
	public function actionForward($request, $response, $router) {
		if (!$this->forward)
			$this->forward = $router->getDefaultViewHandle();
		
		$viewer = WViewFactory::getInstance()->create($this->forward);
		if (!$request->getIsAjaxRequest() && $this->layout instanceof WLayout) {
			$viewer->setLayout($this->layout);
		}
		$viewer->windAssign($this->view);
	}

}