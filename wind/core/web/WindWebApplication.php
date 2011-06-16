<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

Wind::import('WIND:core.WindComponentModule');
Wind::import('WIND:core.web.IWindApplication');
Wind::import('WIND:core.factory.WindComponentDefinition');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindComponentModule implements IWindApplication {

	const ERROR_HANDLER = 'error-handler';

	protected $dispatcher = null;

	protected $errorHandle = 'WIND:core.web.WindErrorHandler';

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
			$this->sendErrorMessage($actionException);
		
		} catch (WindSqlException $windSqlException) {
			$this->sendErrorMessage($windSqlException->getMessage());
			
		} catch (WindViewException $windViewException) {
			
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
		
		if (!strcasecmp($handlerAdapter->getController(), WIND_M_ERROR)) {
			$moduleConfig = $this->windSystemConfig->getModules($this->getModule());
			$handler = $this->windSystemConfig->getConfig(self::ERROR_HANDLER, WIND_CONFIG_CLASS, $moduleConfig, $this->errorHandle);
		} else
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
		$actionHandler->beforeAction($handlerAdapter);
		
		//TODO 添加过滤链
		if ($actionHandler->_getInstance() instanceof WindFormController) {
			if ($formClassPath = $actionHandler->getFormClass()) {
				Wind::import('WIND:core.web.listener.WindFormListener');
				$actionHandler->registerEventListener('doAction', new WindFormListener($this->request, $formClassPath, $actionHandler->getErrorMessage()));
			}
		} elseif ($actionHandler->_getInstance() instanceof WindController) {
			if ($rules = (array) $actionHandler->validatorFormRule($handlerAdapter->getAction())) {
				if (!isset($rules['errorMessage'])) {
					$rules['errorMessage'] = $actionHandler->getErrorMessage();
				}
				Wind::import('WIND:core.web.listener.WindValidateListener');
				$actionHandler->registerEventListener('doAction', new WindValidateListener($this->request, $rules, $actionHandler->getValidatorClass()));
			}
		}
		
		//add log
		if (IS_DEBUG) {
			/* @var $logger WindLogger */
			$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
			$logger->debug('ActionHandler: ' . $handler);
		}
		
		return $actionHandler;
	}

	/**
	 * 错误请求
	 * @param WindActionException actionException
	 */
	protected function sendErrorMessage($actionException) {
		$forward = $this->windFactory->getInstance(COMPONENT_FORWARD);
		$_tmp = $actionException->getError();
		if (is_string($_tmp)) {
			Wind::import('WIND:core.web.WindErrorMessage');
			$_tmp = new WindErrorMessage($_tmp);
		}
		$forward->forwardAnotherAction($_tmp->getErrorAction(), $_tmp->getErrorController());
		$this->request->setAttribute('error', $_tmp->getError());
		$this->doDispatch($forward);
	}

	/**
	 * @param request
	 * @return AbstractWindRouter
	 */
	protected function getHandlerAdapter() {
		$routerAlias = $this->windSystemConfig->getRouter(WIND_CONFIG_CLASS);
		if (null === $this->getAttribute($routerAlias)) {
			/* @var $router AbstractWindRouter */
			$router = $this->windFactory->getInstance($routerAlias);
			if (!$router instanceof AbstractWindRouter) {
				throw new WindException(get_class($this) . '::getHandlerAdapter()', WindException::ERROR_RETURN_TYPE_ERROR);
			}
		}
		return $this->getAttribute($routerAlias);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param string $message
	 */
	protected function noActionHandlerFound($message) {
		$this->response->sendError(WindHttpResponse::SC_NOT_FOUND, $message);
	}

	/* (non-PHPdoc)
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	public function getWriteTableForGetterAndSetter() {
		return array('dispatcher');
	}

}