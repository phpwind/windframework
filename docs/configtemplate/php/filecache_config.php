<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 文件缓存的配置
 */
return array(
    /*缓存文件的保存路径（支持命名空间的方式配置该路径）*/
    'cache-dir' => array('value' => 'WEB:compile'),
    /*缓存的级别*/
    'cache-level' => array('value' => '0'),
    /*缓存文件的后缀*/
    'cache-suffix' => array('value' => 'php'),
    /*缓存文件的key值是否经过安全处理*/
    'security' => array('value' => 'true'),
    /*缓存文件的过期时间*/
    'expires' => array('value' => '20'),
);