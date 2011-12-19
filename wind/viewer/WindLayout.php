<?php
/**
 * 视图布局对象
 * 
 * 通过设置布局模板文件来实现对页面的布局管理,和WindView以及WindViewerResolver配合使用实现对页面的布局管理.
 * 布局文件的路径设置方式与WindView中的模板路径设置方式相同,支持命名空间方式<code>
 * $layout = layoutFile;	//return $templateDir/layoutFile
 * $layout = nameSpace:layoutFile;	//return nameSpace:layoutFile</code>
 * 布局文件例子<code>
 * <!doctype html>
 * <html>
 * <template source='head'/>	//布局文件中有基础的编译支持
 * <body>
 * <div class="wrap">
 * <section class="main">
 * <!--#$this->segment('head');#-->	//布局切片
 * <!--#$this->content();#-->	//调用当前的模板内容
 * </section>
 * </div>
 * </body>
 * </html></code>
 * 组件定义:<code>
 * 'layout' => array(
 * 'path' => 'WIND:viewer.WindLayout',
 * 'scope' => 'prototype',
 * )</code>
 * <note><b>注意:</b>框架默认布局组件</note>
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 */
class WindLayout extends WindModule {
	/**
	 * 布局文件
	 * 
	 * @var string
	 */
	private $layout;
	/**
	 * 视图渲染器对象
	 * 
	 * @var WindViewerResolver
	 */
	private $viewer = null;
	/**
	 * 视图管理器
	 *
	 * @var WindView
	 */
	private $view = null;
	private $segments = array();

	/**
	 * @param string $layoutFile 布局文件
	 * @param WindView $view 视图模板
	 * @param WindViewerResolver $viewer
	 */
	public function __construct($layoutFile, $view, $viewer) {
		$this->layout = $layoutFile;
		$this->view = $view;
		$this->viewer = $viewer;
	}

	/**
	 * 解析布局文件
	 * 
	 * @param WindViewerResolver $viewer
	 * @return void
	 */
	public function parser() {
		ob_start();
		if (method_exists($this->viewer, 'compile')) {
			list($tpl) = $this->viewer->compile($this->layout);
		} else
			$tpl = $this->view->getViewTemplate($this->layout);
		
		if (!@include ($tpl)) {
			throw new WindViewException('[component.viewer.WindLayout.parser] layout file ' . $tpl, 
				WindViewException::VIEW_NOT_EXIST);
		}
		return ob_get_clean();
	}

	/**
	 * 输出模板切片内容
	 * 
	 * @param string $template 模板切片名称
	 * @return void
	 */
	private function segment($template) {
		if ($this->viewer === null || !$template) return '';
		echo $this->viewer->windFetch($template);
	}

	/**
	 * 输出当前模板的内容
	 * 
	 * @return void
	 */
	private function content() {
		if ($this->viewer === null) return '';
		$this->segment($this->view->templateName);
	}
}