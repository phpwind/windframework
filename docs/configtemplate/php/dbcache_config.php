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
     /*配置数据缓存表*/
     'cache-table' => array(
         'table-name' => array('value' => 'pw_cache'),
         'field-key' => array('value' => 'name'),
         'field-value' => array('value' => 'cache'),
         'field-expire' => array('value' => 'time'),
         'expirestrage' => array('value' => 'true'),
     ),
     /*缓存key是否经过安全处理*/
     'security' => array('value' => 'true'),
     /*缓存过期时间*/
     'expires' => array('value' => '20'),
 ); 