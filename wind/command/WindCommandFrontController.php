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
	
	protected $components = 'WIND:command.command.components';
	/**
	 *
	 * @var IWindRequest
	 */
	protected $request = 'WIND:command.WindCommandRequest';
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::_loadBaseLib()
	 */
	protected function _loadBaseLib() {
		return array(
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
		Wind::import('WIND:command.WindCommandApplication');
		$application = new WindCommandApplication($this->request, $this->factory);
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