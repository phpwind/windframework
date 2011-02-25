<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindComponentModule');
L::import('WIND:core.web.IWindApplication');
L::import('WIND:core.factory.WindComponentDefinition');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindComponentModule implements IWindApplication {

	private $errorHandler = null;

	private $dispatcher = null;

	/* (non-PHPdoc)
	 * @see IWindApplication::processRequest()
	 */
	public function processRequest() {
		try {
			//add log
			if (IS_DEBUG) {
				/* @var $logger WindLogger */
				$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
				$logger->debug('do processRequest of ' . get_class($this));
			}
			
			$handler = $this->getHandler();
			$forward = call_user_func_array(array($handler, 'doAction'), array($this->getHandlerAdapter()));
			if ($forward === null) {
				throw new WindException('doAction', WindException::ERROR_RETURN_TYPE_ERROR);
			}
			
			$this->doDispatch($forward);
		} catch (WindActionException $actionException) {
			$forward = $this->windFactory->getInstance(COMPONENT_FORWARD);
			$_tmp = $actionException->getError();
			$forward->forwardAnotherAction($_tmp->getErrorAction(), $_tmp->getErrorController(), $_tmp->getError(), false);
			$this->doDispatch($forward);
			
		} catch (WindSqlException $windSqlException) {
			$this->noActionHandlerFound($windSqlException->getMessage());
		
		} catch (WindViewException $windViewException) {

		} catch (WindException $exception) {
			echo $exception->getMessage();
		
		//$this->noActionHandlerFound($exception->getMessage());
		}
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward) {
		//add log
		if (IS_DEBUG) {
			/* @var $logger WindLogger */
			$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
			$logger->info('do doDispatch of ' . get_class($this));
		}
		
		$this->dispatcher->dispatch($forward);
	}

	/**
	 * 获得Action处理句柄
	 * 
	 * @param WindHttpRequest $request
	 */
	protected function getHandler() {
		$handlerAdapter = $this->getHandlerAdapter();
		$handlerAdapter->doParse();
		
		//add log
		if (IS_DEBUG) {
			/* @var $logger WindLogger */
			$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
			$logger->debug('router result: Action:' . $handlerAdapter->getAction() . ' Controller:' . $handlerAdapter->getController() . ' Module:' . $handlerAdapter->getModule());
		}
		
		$this->checkReprocess($handlerAdapter->getController() . '_' . $handlerAdapter->getAction());
		
		$handler = $handlerAdapter->getHandler();
		$definition = new WindComponentDefinition();
		$definition->setPath($handler);
		$definition->setScope(WindComponentDefinition::SCOPE_PROTOTYPE);
		$definition->setProxy('true');
		$definition->setAlias($handler);
		$definition->setPropertys(array('errorMessage' => array('ref' => COMPONENT_ERRORMESSAGE), 
			'forward' => array('ref' => COMPONENT_FORWARD), 'urlHelper' => array('ref' => COMPONENT_URLHELPER)));
		
		$this->windFactory->addClassDefinitions($definition);
		$actionHandler = $this->windFactory->getInstance($handler);
		
		//TODO 添加过滤链
		

		//add log
		if (IS_DEBUG) {
			/* @var $logger WindLogger */
			$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
			$logger->debug('ActionHandler: ' . $handler);
		}
		
		return $actionHandler;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param string $message
	 */
	protected function noActionHandlerFound($message) {
		$this->response->sendError(WindHttpResponse::SC_NOT_FOUND, $message);
	}

	/**
	 * Enter description here ...
	 * @param request
	 * @return AbstractWindRouter
	 */
	protected function getHandlerAdapter() {
		$routerAlias = $this->windSystemConfig->getRouter(WindSystemConfig::CLASS_PATH);
		if (null === $this->getAttribute($routerAlias)) {
			$router = $this->windFactory->getInstance($routerAlias);
		}
		return $this->getAttribute($routerAlias);
	}

	/**
	 * @param field_type $errorHandler
	 */
	public function setErrorHandler($errorHandler) {
		$this->errorHandler = $errorHandler;
	}

	/**
	 * @param field_type $dispatcher
	 */
	public function setDispatcher($dispatcher) {
		$this->dispatcher = $dispatcher;
	}

}