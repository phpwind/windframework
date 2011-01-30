<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindComponentModule');
L::import('WIND:core.web.IWindApplication');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindComponentModule implements IWindApplication {

	/* (non-PHPdoc)
	 * @see IWindApplication::processRequest()
	 */
	public function processRequest($request, $response) {
		try {
			$this->windConfig = $request->getAttribute(WindFrontController::WIND_CONFIG);
			$this->windFactory = $request->getAttribute(WindFrontController::WIND_FACTORY);
			
			$this->doDispatch($request, $response);
		} catch (WindException $exception) {

		}
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($request, $response) {
		try {
			$handler = $this->getHandler($request, $response);
			$forward = call_user_func_array(array($handler, 'doAction'), array($this->getHandlerAdapter($request)));
			
			$this->render($forward);
		
		} catch (WindException $exception) {
			
			$this->noActionHandlerFound($request, $response, $exception->getMessage());
		}
	}

	/**
	 * @param WindHttpRequest $request
	 */
	protected function getHandler($request, $response) {
		$handlerAdapter = $this->getHandlerAdapter($request);
		$this->checkReprocess($handlerAdapter->getController() . '_' . $handlerAdapter->getAction());
		
		$handler = $handlerAdapter->getHandler($request, $response);
		$handler = $this->windFactory->createInstance($handler, array($request, $response));
		if ($handler instanceof WindAction)
			return $handler;
		else
			throw new WindException('WindAction', WindException::ERROR_CLASS_TYPE_ERROR);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param string $message
	 */
	protected function noActionHandlerFound($request, $response, $message) {
		//TODO
		$response->sendError(WindHttpResponse::SC_NOT_FOUND, $message);
	}

	/**
	 * 判断是否是重复提交，再一次请求中，不允许连续重复请求两次获两次以上某个操作
	 * 
	 * @param string $key
	 */
	protected function checkReprocess($key = '') {
		if (isset($this->process) && $this->process === $key) {
			throw new WindException('Duplicate request \'' . $key . '\'');
		}
		$this->process = $key;
	}

	/**
	 * Enter description here ...
	 * @param request
	 * @return AbstractWindRouter
	 */
	protected function getHandlerAdapter($request) {
		$routerAlias = $this->windConfig->getRouter(WindSystemConfig::CLASS_PATH);
		if (null === $this->getAttribute($routerAlias)) {
			$router = $this->windFactory->getInstance($routerAlias);
			if (IS_DEBUG && $router instanceof WindClassProxy) {
				$router->registerEventListener('doParse', new WindLoggerListener());
				$router->registerEventListener('getHandler', new WindLoggerListener());
				$router->registerEventListener('buildUrl', new WindLoggerListener());
			}
			$router->doParse($request);
		}
		return $this->getAttribute($routerAlias);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param WindForward $forward
	 */
	protected function render($forward) {

	}

}