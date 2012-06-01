<?php
Wind::import('WIND:command.WindCommandController');
/**
 * 命令行模式下默认的错误处理器
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package command
 */
class WindCommandErrorHandler extends WindCommandController {
	/* (non-PHPdoc)
	 * @see WindCommandController::run()
	 */
	public function run($error, $errorCode) {
		$this->setOutput('Error: ');
		foreach ($error as $k => $e) {
			$k++;
			$this->setOutput($k . '.' . $e);
		}
		$this->setOutput('You Can Get Help By :');
		$this->setOutput('[-m module] [-c controller] [-a action] --help');
	}
}

?>