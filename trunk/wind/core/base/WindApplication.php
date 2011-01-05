<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WindApplication {
	abstract public function init($dispatcher);
	abstract public function processRequest($request, $response);
	abstract public function destory();
}