<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class ErrorController extends WindErrorHandler {

	public function run() {
		echo 'error controller.<br><h1>you have error here:</h1><ul>';
		foreach ($this->error as $key => $value) {
			echo '<li>' . $value, '</li><br/>';
		}
		echo '</ul>';
		exit();
	}
}