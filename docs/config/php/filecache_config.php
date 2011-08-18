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
    'expires' => '0',
    'key-prefix' => '',
 	'security-code' => '',
    /*缓存文件的保存路径（支持命名空间的方式配置该路径）*/
    'dir' => 'WIND:_compile',
    /*缓存文件的后缀*/
    'suffix' => 'php',
    'dir-level' => '0',
);