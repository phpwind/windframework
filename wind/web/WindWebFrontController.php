<?php
/**
 * 应用前端控制器
 *
 * 应用前端控制器，负责根据应用配置启动应用，多应用管理，多应用的配置管理等.
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WindFrontController.php 2966 2011-10-14 06:41:59Z yishuo $
 * @package wind
 */
class WindWebFrontController extends AbstractWindFrontController {

	/**
	 * 创建并执行当前应用
	 *
	 * @deprecated
	 * @param string $appName        	
	 * @param string|array $config        	
	 * @return void
	 */
	public function multiRun() {
		$this->initConfig();
		
		/* @var $router WindRouter */
		$router = $this->getFactory()->getInstance('router');
		$this->_appName && $router->setApp($this->_appName);
		$router->route($this->getRequest());
		$this->_appName = $router->getApp();
		$this->_run();
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFrontController::createApplication()
	 */
	protected function _createApplication() {
		$application = new WindWebApplication($this->getRequest(), $this->getFactory());
		$application->setDelayAttributes(
			array('dispatcher' => array('ref' => 'dispatcher'), 'handlerAdapter' => array('ref' => 'router')));
		return $application;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::showErrorMessage()
	*/
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		parent::showErrorMessage($message, $file, $line, $trace, $errorcode);
		if (!empty($this->_config[$this->_appName]['errorDir'])) {
			$errDir = $this->_config[$this->_appName]['errorDir'];
		} else
			$errDir = 'WIND:web.view';
		$errDir = Wind::getRealPath($errDir, false);
		if (is_file($errDir . '/' . $errorcode . '.htm')) $this->_errPage = $errorcode;
		ob_start();
		require $errDir . '/' . $this->_errPage . '.htm';
		exit(ob_get_clean());
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::_components()
	 */
	protected function _components() {
		return array(
			'router' => array('path' => 'WIND:router.WindRouter', 'scope' => 'application'), 
			'windView' => array(
				'path' => 'WIND:viewer.WindView', 
				'scope' => 'application', 
				'config' => array(
					'template-dir' => 'template', 
					'template-ext' => 'htm', 
					'is-compile' => '1', 
					'compile-dir' => 'compile.template', 
					'compile-ext' => 'tpl', 
					'layout' => '', 
					'theme' => '', 
					'htmlspecialchars' => true), 
				'properties' => array(
					'viewResolver' => array('path' => 'WIND:viewer.resolver.WindViewerResolver'), 
					'windLayout' => array('path' => 'WIND:viewer.WindLayout'))), 
			'template' => array('path' => 'WIND:viewer.compiler.WindViewTemplate', 'scope' => 'prototype'), 
			'db' => array('path' => 'WIND:db.WindConnection', 'scope' => 'application'), 
			'configParser' => array('path' => 'WIND:parser.WindConfigParser', 'scope' => 'singleton'), 
			'dispatcher' => array('path' => 'WIND:web.WindDispatcher', 'scope' => 'application'), 
			'forward' => array(
				'path' => 'WIND:web.WindForward', 
				'scope' => 'prototype', 
				'properties' => array('windView' => array('ref' => 'windView'))), 
			'errorMessage' => array('path' => 'WIND:base.WindErrorMessage', 'scope' => 'prototype'), 
			'windLogger' => array(
				'path' => 'WIND:log.WindLogger', 
				'scope' => 'singleton', 
				'destroy' => 'flush', 
				'constructor-args' => array('0' => array('value' => 'DATA:log'), '1' => array('value' => '2'))), 
			'i18n' => array(
				'path' => 'WIND:i18n.WindLangResource', 
				'scope' => 'singleton', 
				'config' => array('path' => 'i18n')));
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::_loadBaseLib()
	 */
	protected function _loadBaseLib() {
		Wind::$_imports += array(
			'WIND:i18n.WindLangResource' => 'WindLangResource', 
			'WIND:log.WindLogger' => 'WindLogger', 
			'WIND:core.web.WindErrorMessage' => 'WindErrorMessage', 
			'WIND:web.WindForward' => 'WindForward', 
			'WIND:web.WindDispatcher' => 'WindDispatcher', 
			'WIND:parser.WindConfigParser' => 'WindConfigParser', 
			'WIND:db.WindConnection' => 'WindConnection', 
			'WIND:viewer.compiler.WindViewTemplate' => 'WindViewTemplate', 
			'WIND:viewer.WindView' => 'WindView', 
			'WIND:router.WindRouter' => 'WindRouter', 
			'WIND:web.WindController' => 'WindController', 
			'WIND:web.WindDispatcher' => 'WindDispatcher', 
			'WIND:web.WindErrorHandler' => 'WindErrorHandler', 
			'WIND:web.WindForward' => 'WindForward', 
			'WIND:web.WindHttpRequest' => 'WindHttpRequest', 
			'WIND:web.WindHttpResponse' => 'WindHttpResponse', 
			'WIND:web.WindSimpleController' => 'WindSimpleController', 
			'WIND:web.WindUrlHelper' => 'WindUrlHelper', 
			'WIND:web.WindWebApplication' => 'WindWebApplication');
		
		Wind::$_classes += array(
			'WindLangResource' => 'i18n/WindLangResource', 
			'WindLogger' => 'log/WindLogger', 
			'WindErrorMessage' => 'core/web/WindErrorMessage', 
			'WindForward' => 'web/WindForward', 
			'WindDispatcher' => 'web/WindDispatcher', 
			'WindConfigParser' => 'parser/WindConfigParser', 
			'WindConnection' => 'db/WindConnection', 
			'WindViewTemplate' => 'viewer/compiler/WindViewTemplate', 
			'WindView' => 'viewer/WindView', 
			'WindRouter' => 'router/WindRouter', 
			'WindController' => 'web/WindController', 
			'WindDispatcher' => 'web/WindDispatcher', 
			'WindErrorHandler' => 'web/WindErrorHandler', 
			'WindForward' => 'web/WindForward', 
			'WindHttpRequest' => 'web/WindHttpRequest', 
			'WindHttpResponse' => 'web/WindHttpResponse', 
			'WindSimpleController' => 'web/WindSimpleController', 
			'WindUrlHelper' => 'web/WindUrlHelper', 
			'WindWebApplication' => 'web/WindWebApplication');
	}
}

?>