<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WMsg{

	private static $message = array();
	public  static $postTpl = 'post';

	public static function redirect($url,$msg='',$timer = 0){
	    if (!headers_sent()) {
	        header("HTTP/1.1 301 Moved Permanently");
	      	$timer ? header("refresh:{$timer};url={$url}") : header("Location: ".$url);
	        exit($msg);
	    }
	    exit("<meta http-equiv='Refresh' content='{$timer};URL={$url}'/>$msg");
	}
	
	public static function addMsg($msg,$name = ''){
		$name ? self::$message[$name] = $msg : self::$message[] = $msg ;
	}
	
	public static function getAllMsg(){
		return  self::$message;		
	}
	
	public static function getMsg($name=''){
		return $name ? self::$message[$name] : array_shift(self::$message);
	}
	
	public static function showMsg($name='',$url=array(),$icon = 'error',$timer = 1,$ifpost=0){
		$msg = self::getMsg($name);
	}
	
	public static function msg($msg,$url=array(),$icon = 'error',$timer = 1,$ifpost=0){
		
	}

}