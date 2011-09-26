<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
###################################################################
#######db缓存配置格式：############################################
return array(
	//缓存文件的过期时间
    'expires' => '0',  
	//缓存key的前缀
    'key-prefix' => '',
	//缓存key安全盐码
 	'security-code' => '',
	//缓存的表名
    'table-name' => 'pw_cache',
	//缓存的key字段名称 
    'field-key' => 'key',
	//缓存的value字段名称 
    'field-value' => 'value',
	//缓存的过期时间字段名称
    'field-expire' => 'expire',
 );
 
 
 
###################################################################
#######File缓存配置格式：############################################
return array(
	//缓存文件的过期时间
    'expires' => '0',
	//缓存key的前缀
    'key-prefix' => '',
	//缓存key安全盐码
 	'security-code' => '',
    //缓存文件的保存路径（支持命名空间的方式配置该路径）
    'dir' => 'WIND:_compile',
    //缓存文件的后缀
    'suffix' => 'php',
	//缓存的目录支持级别
    'dir-level' => '0',
);



###################################################################
######Memcache缓存配置格式：############################################
return array(
	//缓存文件的过期时间
    'expires' => '0',
	//缓存key的前缀
    'key-prefix' => '',
	//缓存key安全盐码
 	'security-code' => '',
 	/*压缩的级次*/
    'compress' => '0',
     /*memcache服务器相关配置 可以配置多个*/
    'servers' => array(
		//例如配置test1和test2两台主机
    	'test1' => array(
    		'host' => '127.0.0.1',  #memcache主机ip
    		'port' => '11211', 		#memcache端口
			'weight' => 1,		    #为此服务器创建的桶的数量，用来控制此服务器被选中的权重
			'pconn' => true,		#是否使用长连
			'timeout' => 1,			#连接持续（超时）时间（单位秒）
			'retry' => 15,			#服务器连接失败时重试的间隔时间
			'status' => true,		#控制此服务器是否可以被标记为在线状态
			'fcallback' => '',		#允许用户指定一个运行时发生错误后的回调函数
		), 
		'test2' => array('host' => '127.0.0.1', 'port' => '11212', 'weight' => 1)
    ),
);