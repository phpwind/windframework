<?php
/**
 * wind core 基础帮助类
 * 不建议被外部调用
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHelper {

	/**
	 * 解析ControllerPath
	 * 返回解析后的controller信息，controller，module，app
	 * 
	 * @param string $controllerPath
	 * @return array
	 */
	public static function resolveController($controllerPath) {
		$_m = $_c = '';
		if (!$controllerPath) return array($_c, $_m);
		if (false !== ($pos = strrpos($controllerPath, '.'))) {
			$_m = substr($controllerPath, 0, $pos);
			$_c = substr($controllerPath, $pos + 1);
		} else {
			$_c = $controllerPath;
		}
		return array($_c, $_m);
	}
}
?>