<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package wind
 */
class CompileCmdHelp {

	/**
	 * 获取用户输入信息
	 * 
	 * @param string $message
	 * @return string
	 */
	static public function getInput($message) {
		echo $message;
		return str_replace(array("\r", "\n"), '', fgets(STDIN));
	}

	/**
	 * 显示提示信息
	 *
	 * @param string $message
	 */
	static public function showMessge($message) {
		if (is_array($message)) {
			foreach ($message as $key => $value) {
				echo "'" . $key . "' => '" . $value . "',\r\n";
			}
		} else
			echo $message;
	}

	/**
	 * 显示系统错误信息
	 *
	 * @param string $message
	 * @param string $cmd
	 */
	static public function showError($message, $cmd = 'pack') {
		$_msg = "Error: " . $message . "\r\n";
		$_msg .= "Try: " . $cmd . " help for usage\r\n";
		exit($_msg);
	}
}

?>