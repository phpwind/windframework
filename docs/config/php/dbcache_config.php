<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 数据库缓存的配置信息
 */
 return array(
    'expires' => '0',
    'key-prefix' => '',
 	'security-code' => '',
     /*配置数据缓存表*/
 	'dbconfig-name' => 'test',
    'table-name' => 'pw_cache',
    'field-key' => 'key',
    'field-value' => 'value',
    'field-expire' => 'expire',
 ); 