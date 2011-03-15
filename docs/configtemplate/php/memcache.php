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
    /*memcache服务器相关配置 可以配置多个*/
    'servers' => array(
    	'phpwind' => array('host' => '127.0.0.1', 'port' => '11211'), 
		'cac' => array('host' => '127.0.0.1', 'port' => '11212')
    ),
    /*压缩的级次*/
    'compress' => array('value' => '2'),
    /*缓存key是否经过安全过滤*/
    'security' => array('value' => 'true'),
    /*缓存过期时间*/
    'expires' => array('value' => '20'));