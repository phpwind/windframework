<?php
return array(

	/*站点是否关闭设置*/
	'isclose' => '1',

	/*站点关闭后转向的模板路径*/
	'isclose-tpl' => 'TEST:template.closed.htm',

	/*组件配置：
	* 组件的配置将会覆盖框架提供的组件的默认配置行为*/
	'components' => array(
		/*可以根据自己的需求重新配置组件的相关项*/
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
			/*当开启多应用时候，路由组件需指向WindMultiAppRouter*/
			'path' => 'WIND:router.WindMultiAppRouter',
            'config' => array(
				/*当开启多应用时候，可配置路径中app的规则*/
			    'app' => array(
			         'url-param' => 'p',
			         'default-value' => 'default',
			     ),
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
				/*路由协议配置*/
				'routes' => array(
					'WindRoute' => array(
						'class' 	=> 'WIND:router.route.WindRewriteRoute',
						/*正则匹配*/
						'pattern' 	=> '^http[s]?:\/\/[^\/]+(\/\w+)?(\/\w+)?(\/\w+)?.*$',
						'params' 	=> array(
							'a'	=> array('map'  => 3, 'default' => 'run'),
							'c'	=> array('map'	=> 2),
							'm'	=> array('map'  => 1),
						),
					),
				),
			),
		),
	),
	
	/*应用配置*/
	'web-apps' => array(
		/*配置应用项名为default*/
		/*name为default的应用将会作为缺省的应用配置，其他应用的配置将会和default配置进行merge；*/
		'default' => array(
			'class' => 'windApplication',
			/*过滤链配置，可以配置通用的，同时也可以配置Module级别的，controller级别的，action级别的；*/
			'filters' => array(
				/*过滤器的配置*/
				'formfilter' => array(
					/*过滤器实现*/
					'class' => 'WIND:web.filter.WindFormFilter',
					/*过滤器适用的范围：
			    	 * *：则所有的请求都将会应用该过滤器
			    	 * module/*: 则所有module模块的请求都将会应用该过滤器
			    	 * module/index/*: 则module模块下的indexController下的所有Action请求都将会应用该过滤器
			    	 * module/index/add: 则module模块下的indexController下的addAction将会应用该过滤器
			    	*/
					'pattern' => '*',
					/*使用框架提供的formFilter的时候配置给该filter指明需要使用的form*/
					'form' => 'MyForm',
					/*其他配置项：将会传递给配置的filter相同配置项的属性*/
				)
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
	),
);