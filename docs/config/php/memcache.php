<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * memcache配置
 */
return array(
	'expires' => '0',
    'key-prefix' => '',
 	'security-code' => '',
 	/*压缩的级次*/
    'compress' => '2',
    /*memcache服务器相关配置 可以配置多个*/
    'servers' => array(
    	'phpwind' => array(
    		'host' => '127.0.0.1', 
    		'port' => '11211',
			'pconn' => true,
			'weight' => 1,
			'timeout' => 15,
			'retry' => 15,
			'status' => true,
			'fcallback' => '',
		), 
		'cac' => array('host' => '127.0.0.1', 'port' => '11212')
    ),
);
    