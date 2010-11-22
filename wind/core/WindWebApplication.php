<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.impl.WindApplicationImpl');
L::import('WIND:component.exception.WindException');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication implements WindApplicationImpl {
	
	/**
	 * 初始化配置信息
	 * @param WSystemConfig $configObj
	 */
	public function init() {}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WSystemConfig $configObj
	 */
	public function processRequest($request, $response) {
		$router = $this->createRouter();
		$router->doParser($request, $response);
		
		list($action, $method) = $this->getActionHandle($request, $response, $router);
		
		$this->processActionForm($request, $response, $router);
		
		$action->beforeAction();
		$action->$method($request, $response);
		$action->afterAction();
		$action->actionForward($request, $response, $router);
		
		$this->processActionForward($request, $response);
	}
	
	/**
	 * 返回action类
	 */
	protected function getActionHandle($request, $response, $router) {
		$configObj = WindSystemConfig::getInstance();
		list($className, $method) = $router->getControllerHandle($request, $response);
		if ($className === null || $method === null) {
			list($className, $method) = $router->getActionHandle($request, $response);
		}
		if ($className === null || $method === null) {
			throw new WindException('get controller handle is failed.');
		}
		$class = new ReflectionClass($className);
		$action = call_user_func_array(array($class, 'newInstance'), array($request, $response));
		return array($action, $method);
	}
	
	/**
	 * 自动设置actionform对象
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WRouter $router
	 */
	protected function processActionForm($request, $response, $router) {
		if (($formHandle = $router->getActionFormHandle()) == null) return;
		
		/* @var $actionForm WActionForm */
		$actionForm = W::getInstance($formHandle, array($request, $response));
		if ($actionForm->getIsValidation()) $actionForm->validation();
	}
	
	/**
	 * 处理页面输出与重定向
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WActionForward $forward
	 */
	protected function processActionForward($request, $response) {
		W::import('WIND:components.viewer.*');
		$viewer = WViewFactory::getInstance()->create();
		if ($viewer == null) throw new WindException('The instance of viewer is null.');
		$response->setBody($viewer->windDisplay());
	}
	
	/**
	 * 获得一个路由实例
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	public function &createRouter() {
		$configObj = WindSystemConfig::getInstance();
		$parser = $configObj->getRouterConfig('parser');
		$parserPath = $configObj->getRouterParser($parser);
		list(, $className, , $parserPath) = L::getRealPath($parserPath, true);
		L::import($parserPath);
		if (!class_exists($className)) throw new WindException('The router ' . $className . ' is not exists.');
		$router = new $className($configObj);
		return $router;
	}
	
	public function destory() {}
}