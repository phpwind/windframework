<?php
/**
 * 命令行前端控制器
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package command
 */
class WindCommandFrontController extends AbstractWindFrontController {
	
	protected $components = 'command/command.components';
	
	/**
	 * @return WindHttpRequest
	 */
	public function getRequest() {
		if ($this->_request === null) {
			Wind::$_classes['WindCommandRequest'] = 'command/WindCommandRequest';
			$this->_request = WindFactory::createInstance('WindCommandRequest');
		}
		return $this->_request;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::_loadBaseLib()
	 */
	protected function _loadBaseLib() {
		Wind::$_imports += array(
		'WIND:command.WindCommandErrorHandler' => 'WindCommandErrorHandler',
		'WIND:command.WindCmmandRequest' => 'WindCommandRequest',
		'WIND:command.WindCommandResponse' => 'WindCommandResponse',
		'WIND:command.WindCommandController' => 'WindCommandController',
		'WIND:command.WindCommandApplication' => 'WindCommandApplication');
		
		Wind::$_classes += array(
			'WindCommandApplication' => 'command/WindCommandApplication', 
			'WindCommandController' => 'command/WindCommandController', 
			'WindCommandErrorHandler' => 'command/WindCommandErrorHandler', 
			'WindCommandRequest' => 'command/WindCommandRequest', 
			'WindCommandResponse' => 'command/WindCommandResponse');
	}
	
	/**
	 * 创建并返回应用
	 *
	 * @return WindCommandApplication
	 */
	protected function _createApplication() {
		$application = new WindCommandApplication($this->getRequest(), $this->getFactory());
		$application->setDelayAttributes(
			array(
				'windView' => array('ref' => 'windView'), 
				'handlerAdapter' => array('ref' => 'router')));
		return $application;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::showErrorMessage()
	 */
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		// TODO Auto-generated method stub
	}

}
?>