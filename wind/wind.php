<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 * @version $Id$
 */


require 'WindBase.php';
$preload = WIND_PATH.PRELOAD_FILE;
if(!is_file($preload)){
	 require $preload;
}else{
	L::import('WIND:utility.WindPackge');
	$pack = L::getInstance('WindPackge');
	$pack->pack(array('core','utility'),PRELOAD_FILE);
	require $preload;

}




