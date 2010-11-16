<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WWebApplicationController implements WApplicationController {
	
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
		$configObj = W::getInstance('WSystemConfig');
		if (($base = $configObj->getConfig('controller')) == 'WActionController')
			list($className, $method) = $router->getControllerHandle($request, $response);
		
		elseif (($base = $configObj->getConfig('controller')) == 'WAction')
			list($className, $method) = $router->getActionHandle($request, $response);
		
		else
			throw new WException('determine the baseController is failed in config.php.');
		
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
		if (($formHandle = $router->getActionFormHandle()) == null)
			return;
			
		/* @var $actionForm WActionForm */
		$actionForm = W::getInstance($formHandle, array($request, $response));
		if ($actionForm->getIsValidation())
			$actionForm->validation();
	}
	
	/**
	 * 处理页面输出与重定向
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WActionForward $forward
	 */
	protected function processActionForward($request, $response) {
		$viewer = WViewFactory::getInstance()->create();
		if ($viewer == null)
			throw new WException('The instance of viewer is null.');
		
		$response->setBody($viewer->display());
	}
	
	/**
	 * 获得一个路由实例
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	public function &createRouter() {
		$parser = 'url';
		$parserPath = 'router.parser.WUrlRouteParser';
		$configObj = W::getInstance('WSystemConfig');
		
		if (($_parser = $configObj->getRouterConfig('parser')) != null)
			$parser = $_parser;
		if (($_parserPath = $configObj->getRouterParser($parser)) != null)
			$parserPath = $_parserPath;
		if (($pos = strrpos($parserPath, '.')) === false)
			$className = $parserPath;
		else
			$className = substr($parserPath, $pos + 1);
		W::import($parserPath);
		if (!class_exists($className))
			throw new WException('The router ' . $className . ' is not exists.');
		
		$class = new ReflectionClass($className);
		$router = call_user_func_array(array($class, 'newInstance'), array($configObj));
		return $router;
	}
	
	public function destory() {}
}