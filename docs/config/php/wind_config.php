<?php
return array(
     /*配置应用项名为default*/
	'default' => array(
		'class' => 'windWebApp',

		/*过滤链配置，可以配置通用的，同时也可以配置Module级别的，controller级别的，action级别的；*/
		'filters' => array(
			/*过滤器的配置*/
			'formfilter' => array(
				/*过滤器实现*/
				'class' => 'WIND:web.filter.WindFormFilter',
				/*过滤器适用的范围：
		    	 * *：则所有的请求都将会应用该过滤器
		    	 * module*: 则所有module模块的请求都将会应用该过滤器
		    	 * module_index*: 则module模块下的indexController下的所有Action请求都将会应用该过滤器
		    	 * module_index_add*: 则module模块下的indexController下的addAction将会应用该过滤器
		    	*/
				'pattern' => '*',
				/*使用框架提供的formFilter的时候配置给该filter指明需要使用的form*/
				'form' => 'MyForm',
				/*其他配置项：将会传递给配置的filter相同配置项的属性*/
			)
		),
		
		/*组件配置：
		 * 组件的配置将会覆盖框架提供的组件的默认配置行为*/
		'components' => array(
			/** 比如我配置db组件 **/
			/*db相关配置，如果设置了resource则系统会默认找到resource指向的文件作为db配置信息.DB的配置config内容参照DOCS下的db_config配置*/
			'db' => array(
				'config' => array(
					'resource' => 'db_config.ini'
				),
			),
			/** 比如路由的组件配置 **/
			/*配置default应用的路由规则*/
			'router' => array(
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
                     /*正则匹配*/
                     'routes' => array(
						'WindRoute' => array(
                     		'class' 	=> 'WIND:router.route.WindRoute',
                     		'params' 	=> array(
                     			'script' => array('map' => 1, 'default' => 'index.php'),
                     			'a'	=> array('map'  => 2, 'default' => 'run'),
                     			'c'	=> array('map'	=> 3),
                     			'm'	=> array('map'  => 4),
                     		),
						),
					),
                ),
			),
			/*可以根据自己的需求重新配置组件的相关项*/
		),
		
		/*iscache属性控制windCache组件是否可用，如果关闭则windCache组件将不可用,通过Wind::getApp()->getComponent('windCache');*/
		'iscache' => 1,
		
		/*输出编码设置*/
		'charset' => 'utf-8',
		
		/*模块配置： 可以通过设定多个module来设置多组模块配置，每组模块以name来相互区分*/
		'modules' => array(
			 /** 比如配置default模块 **/
			/*模块名称为default*/
			'default' => array(
			    /*default模块的路径*/
				'controller-path' => 'controller',  
			    /*default模块的中controller的后缀*/
				'controller-suffix' => 'Controller',
				/*配置该模块的error处理的action controller类*/
				'error-handler' => 'WIND:web.WindErrorHandler',
				/*default模块的视图目录配置*/
				'template-dir' => 'template',
				/*default模块的编译目录配置*/
				'compile-dir' => 'data.template',
			),
		),
	),
);