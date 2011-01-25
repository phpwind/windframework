<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once "core/filter/WindFilter.php";

class ForWindFilter extends WindFilter {
	public function preHandle() {
		$args = func_get_args();
		$a = isset($args[0]) ? trim($args[0]) : 'a';
		$b = isset($args[1]) ? trim($args[1]) : 'b';
		echo "{$a}+{$b}";
	}
	public function postHandle() {
		$args = func_get_args();
		$a = isset($args[0]) ? trim($args[0]) : '';
		$b = isset($args[1]) ? trim($args[1]) : '';
		echo "\$a={$a},\$b={$b}";
	}
}