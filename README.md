##关于框架

windframework是一个轻量级的php开源框架。它以组件工厂为核心，提供了像MVC，数据持久化，视图模板引擎等应用技术。此外`windframework`采取了一种组件化的开发模式。虽然目前的组件库还不算丰富健壮，但是基于这种开发模式，使组件的扩展变得非常的容易。`windframework`拥有一个强健的内核，它实现了基于配置的Ioc控制反转技术。开发者只需要简单的配置，就可以实现类的依赖注入，完全实现了类与类的松耦合。当然它的美妙之处还不仅于此。

经典的`hello world`应用实例，用一个非常熟悉的应用来开启一个新框架的学习应用吧！这个应用实例会一如既往的在浏览器窗口打印`Hello World`字样。完成这个应用我们将了解到：

* 如何获取windframework框架源码

* windframework框架环境需求

* windframework基础的目录结构和默认运行规则

* 你还会创建自己的第一个action controller
	
当然这个应用非常的简单，我们应该可以很快的完成这个应用并看到`hello world`的输出。我想这应该快速的拉近了我们之间的距离，但是想要做出强大的应用只是这样还远远不够。

##获取源码

通过[https://github.com/phpwind/windframework/]([https://github.com/phpwind/windframework, "windframework")获取源码。

##环境要求：

* php5.1.2及以上版本。
* 可发布的web环境，apache或aginx

##开始我们的helloworld之旅

###创建应用目录文件

我们在web的根目录（`/var/www`或者其他地方）创建一个文件夹，命名为helloworld。将下载好的框架解压并放到该应用目录下。创建好的目录结构如下：

	/var/www/helloworld/
	wind/					            框架目录
	controller/				            应用控制器目录，业务代码放在该目录下
	controller/IndexController.php		默认访问的应用控制器
	template/				            页面模板目录
	template/index.htm			        模板文件
	index.php				            入口脚本文件


###编辑入口脚本index.php

在应用目录下创建入口脚本index.php，它的主要工作是加载框架并启动应用。代码如下：

	require_once ('../../wind/Wind.php');
	Wind::application()->run();

*PS:当然也可以同时在index.php中设置错误级别，WIND_DEBUG模式等。相关内容后面会介绍*

###创建IndexController.php

在应用目录下创建controller/目录。controller目录是windframework默认定义的应用控制器存放的目录，我们也可以通过手动配置的方式来改变应用的访问路径。在我们创建的 controller/ 目录下创建IndexController.php类文件。文件内容如下：

	<?php
	/**
	* the last known user to change this file in the repository  <$LastChangedBy: long.shi $>
	* @author Qiong Wu <papa0924@gmail.com>
	* @version $Id: IndexController.php 2806 2011-09-23 03:28:55Z long.shi $
	* @package 
	*/
	class IndexController extends WindController {

		public function run()  {
			echo 'hello world';
		}
	}
	?>

*在windframework中文件名和类名是相同的，这一点有点类似于java。windframework提供了两个应用控制器的类型‘WindSimpleController’，‘WindController’。在这里我们继承自‘WindController’，这两个应用控制器的区别，在后面会具体介绍。*

至此，我们的`hello world` 应用已经完成。快通过浏览器访问下我们的`hello world`吧:

	http://localhost/helloworld/index.php 
