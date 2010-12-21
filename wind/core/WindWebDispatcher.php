<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindDispatcher');
/**
 * 请求转发及页面重定向
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebDispatcher extends WindDispatcher {
	private $views = array();
	
	/**
	 * 请求分发一个模板请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function dispatchWithTemplate() {
		$viewResolver = $this->getView()->initViewWithForward($this->forward)->createViewerResolver();
		$viewResolver->windAssign($this->forward->getVars());
		$viewName = $this->forward->getTemplateName();
		if ($this->immediately) {
			$viewResolver->immediatelyWindFetch();
		} else {
			$this->response->setBody($viewResolver->windFetch(), $viewName);
		}
	}
	
	/**
	 * 获得windview对象
	 * @return WindView
	 */
	private function getView() {
		if (!($templateConfigName = $this->forward->getTemplateConfig())) {
			$_temps = $this->modules[$this->module];
			isset($_temps[IWindConfig::MODULE_TEMPLATE]) && $templateConfigName = $_temps[IWindConfig::MODULE_TEMPLATE];
		}
		if (!isset($this->views[$templateConfigName])) {
			L::import('WIND:component.viewer.WindView');
			$view = new WindView($templateConfigName);
			$view->dispatcher = $this;
			$this->views[$templateConfigName] = $view;
		}
		return $this->views[$templateConfigName];
	}
}