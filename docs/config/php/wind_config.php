<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
return array(
     /*配置应用项名为default*/
	'default' => array(
		'class' => 'windWebApp',
		'filters' => array(
			'class' => 'WIND:core.filter.WindFilterChain',
			'filter1' => array('class' => 'WIND:core.web.filter.WindLoggerFilter'),
			'filter2' => array('class' => 'WIND:core.web.filter.WindUrlFilter'),
		),
            /*配置default应用的路由规则*/
		'router' => array(
			'class' => 'urlBasedRouter',
            'config' => array(
            /*配置路径中module的规则*/
        	   	 'module' => array(
	        	     'url-param' => 'm',
	            	 'default-value' => 'default',
             	 ),
           /*配置路径中controller的规则*/
                 'controller' => array(
                     'url-param' => 'c',
                     'default-value' => 'index',
                  ),
             /*配置路径中action的规则*/
                  'action' => array(
                     'url-param' => 'a',
                     'default-value' => 'run',
                  ),
            ),
		),
			/*配置default应用的模块配置*/
		'modules' => array(
			    /*配置default模块*/
			'default' => array(
			       /*default模块的路径*/
				'controller-path' => 'controller',  
			        /*default模块的中controller的后缀*/
				'controller-suffix' => 'Controller',
					/*default模块中处理error的Action controller路径*/
				'error-handler' => 'WIND:core.web.WindErrorHandler',
					/*default模块的试图配置*/
				'view' => array(
					'class' => 'windView',
					'config' => array(
					       /*模板路径*/
						'template-dir' => 'template',
					       /*模板后缀*/
						'template-ext' => 'htm',
					       /*模板编译路径*/
						'compile-dir' => 'compile.template',
					),
				),
			),
		),
		/*db配置*/
		'db' => array(
		//   'resource' => 'db_config.php',
			'conn1' => array(
				'class' => 'COM:db.WindConnection',
				'dsn' => 'mysql:host=localhost;dbname=test',
				'user' => 'root',
				'pwd' => 'root',
				'charset' => 'utf8', 
				'tablePrefix' => 'pw_'
			),
		),
	),
);