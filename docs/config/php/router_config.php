<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

return array(
	/*配置default应用的路由规则*/
	//MVC 配置，url-param：配置访问的别名，default-value:配置缺省值
    /*当开启多应用时候，路由组件指向WindMultiAppRouter时候，可配置路径中app的规则*/
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
	//路由规则配置
	/*用户可以轻易的实现自己的route，继承AbstractWindRoute，同时配置到routes下即可，所有的route下的规则根据自己的实现进行更改调整config*/
	/*正则匹配*/
	'routes' => array(
		'WindRoute' => array(
			//路由的实现
			'class' 	=> 'WIND:router.route.WindRewriteRoute',
			/*正则匹配规则*/
			'pattern' 	=> '^http[s]?:\/\/[^\/]+(\/\w+)?(\/\w+)?(\/\w+)?.*$',
			//参数匹配设置，针对正则中的每个匹配项
			'params' 	=> array(
				//参数的名字:a,  map:匹配上述正则中的子匹配的位置, default:缺省的值
				'a'	=> array('map'  => 3, 'default' => 'run'),
				'c'	=> array('map'	=> 2),
				'm'	=> array('map'  => 1),
			),
			//普通参数的链接分隔符，支持两个字符的配置，第一个字符为参数之间的配置，第二个字符为参数key-value之间的分隔符，默认为&= 
			'separator' => '&=',
			//build的格式，将会依次根据params中配置的map的顺序依次替换%s占位符，普通变量将会跟在其之后 
			'reverse' => '/%s',
		),
	),
);