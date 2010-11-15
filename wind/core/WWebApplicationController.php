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
	public function processRequest($request, $response, $configObj) {
		$router = $this->createRouter($configObj);
		$router->doParser($request, $response);
		if (($base = $configObj->getConfig('baseController')) == 'WActionController')
			list($className, $method) = $router->getControllerHandle($request, $response);
		elseif (($base = $configObj->getConfig('baseController')) == 'WAction')
			list($className, $method) = $router->getActionHandle($request, $response);
		else
			throw new WException('determine the baseController is failed in config.php.');
		
		$class = new ReflectionClass($className);
		$action = call_user_func_array(array(
			$class, 
			'newInstance'
		), array(
			$request, 
			$response
		));
		
		if (($formHandle = $router->getActionFormHandle()) != null) {
			$actionForm = W::getInstance($formHandle, array(
				$request, 
				$response
			));
			
			//TODO 验证表单，并错误处理
			if ($actionForm->getIsValidation()) {
				$this->validateProcessActionForm($actionForm);
			}
		}
		
		$action->$method();
		$viewer = $action->getViewer($configObj, $router);
		
		$this->processActionForward($request, $response, $viewer);
	}
	
	/**
	 * @param WActionForm $actionForm
	 */
	protected function validateProcessActionForm($actionForm) {
		$result = $actionForm->validation();
	}
	
	/**
	 * 处理页面输出与重定向
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WViewer $viewer
	 */
	protected function processActionForward($request, $response, $viewer) {
		
		echo $viewer->display();
	}
	
	/**
	 * 获得一个路由实例
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	public function &createRouter($configObj) {
		$parser = 'url';
		$parserPath = 'router.parser.WUrlRouteParser';
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
		$router = call_user_func_array(array(
			$class, 
			'newInstance'
		), array(
			$configObj
		));
		return $router;
	}
	
	public function destory() {}
}