<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 * @version $Id$
 */

/*
 * 加载类库，并初始化核心文件
 * */
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
//require 'WindBase.php';
echo '<pre>';
$preload = WIND_PATH.'WindPreload.php';
if(!is_file($preload)){
	//L::import($preload);
	require $preload;
}else{
	//L::import('WIND:utility.WindPackge');
	require WIND_PATH.'/utility'.'/WindPackge.php';
	//$pack = L::getInstance('WindPackge');
	$pack = new WindPack();
	$pack->pack(array('core','utility'),'WindPreload.php');
	foreach($pack->getPackList() as $key=>$value){
		//L::regiserImport($key,$value);
	}
	print_r($pack->getPackList());
	//require $preload;
	
	$pack->setPackList('WindAction','asdfafafa');
	$str = '/*'.$pack->formatPackList(true).'*/';
	preg_match_all('/\*(\w+=>.+)/',$str,$match);
	print_r($match);
}




