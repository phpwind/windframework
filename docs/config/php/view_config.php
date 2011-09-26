<?php
/**
 * 试图组件的配置有两部分:
 * 一部分是试图组件的基本配置,包括模板路径编译路径及编译控制等
 * 第二部分是模板编译标签的配置,用户可以配置自己定义的编译解析规则
 */

//第一部分：试图组件的配置
return array(
	//指定模板路径
	'template-dir' => 'template',
	//指定模板后缀
	'template-ext' => 'htm',
	//模板编译文件存放路径
	'compile-dir' => 'data.template',
	//是否编译：如果为0则不编译模板，直接读取已经编译好的模板编译文件，如果为1则编译模板
	'is-compile' => '0',
	//编译模板的后缀配置 
	'compile-ext' => 'tpl',
	//布局文件配置
	'layout' => '',
	//主题包位置
	'theme' => '',
	//是否开启对输出模板变量进行过滤
	'htmlspecialchars' => true,
	
);

//第二部分：模板自定义标签配置
return array(
	'support-tags' => array(
		/** 比如配置：标签tag1 **/
		'tag1' => array(
			//标签定义
			'tag' => '',
			//标签匹配表达式
			'pattern' => '',
			//标签的解析类文件
			'compiler' => '',
		),
 	),
);