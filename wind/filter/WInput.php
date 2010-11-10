<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
/**
 * 全局性过滤，过滤通过GET\POST\COOKIE\SERVER\FILES\GLOBALS中的内容
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WInput extends WFilter {
	/**
	 * 过滤输入
	 * @see security::filter
	 */
	protected function doBeforeProcess($request, $response) {
		$allowed = array('GLOBALS' => 1,'_GET' => 1,'_POST' => 1,'_COOKIE' => 1,'_FILES' => 1,'_SERVER' => 1,
						'P_S_T' => 1);
		foreach ($GLOBALS as $key => $value) {
			if (!isset($allowed[$key])) {
				$GLOBALS[$key] = null;
				unset($GLOBALS[$key]);
			}
		}
		if (!get_magic_quotes_gpc()) {
			WInput::slashes($_POST);
			WInput::slashes($_GET);
			WInput::slashes($_COOKIE);
		}
		WInput::slashes($_FILES);
		/*$GLOBALS['pwServer'] = S::getServer(array('HTTP_REFERER','HTTP_HOST','HTTP_X_FORWARDED_FOR','HTTP_USER_AGENT',
													'HTTP_CLIENT_IP','HTTP_SCHEME','HTTPS','PHP_SELF',
													'REQUEST_URI','REQUEST_METHOD','REMOTE_ADDR','SCRIPT_NAME',
													'QUERY_STRING'));
		!$GLOBALS['pwServer']['PHP_SELF'] && $GLOBALS['pwServer']['PHP_SELF'] = S::getServer('SCRIPT_NAME');*/
		//输入参数安全行验证
		foreach ($_POST as $_key => $_value) {
			if (!in_array($_key,array('atc_content','atc_title','prosign','pwuser','pwpwd'))) {
				WInput::checkVar($_POST[$_key]);
			}
		}
		foreach ($_GET as $_key => $_value) {
			WInput::checkVar($_GET[$_key]);
		}
		echo __CLASS__ . ' do before <br>';
	}
	
	protected function doAfterProcess($request, $response) {
		echo __CLASS__ . ' do after <br>';
	}
	
	/**
	 * 转义函数~~~此函数的位置可以考虑移植到工具类中
	 * @see security::checkVar
	 * @param $array
	 */
	public static function slashes(&$array) {
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					WInput::slashes($array[$key]);
				} else {
					$array[$key] = addslashes($value);
				}
			}
		}
	}
	
	/**
	 * 变量检查~~~此函数的位置可以考虑移植到工具类中
	 * @see security::checkVar
	 *  @param mix &$var
	 */
	public static function checkVar(&$var) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				WInput::checkVar($var[$key]);
			}
		} elseif (P_W != 'admincp') {
			$var = str_replace(array('..',')','<','='), array('&#46;&#46;','&#41;','&#60;','&#61;'), $var);
		} elseif (str_replace(array('<iframe','<meta','<script'), '', $var) != $var) {
			throw new WException('word_error');
		}
	}
}