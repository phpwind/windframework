<?php
Wind::import('WIND:viewer.IWindViewerResolver');
Wind::import('WIND:viewer.exception.WindViewException');
/**
 * 视图渲染器引擎
 * 
 * 该类实现了接口<i>IWindViewerResolver</i>,主要职责是进行视图渲染，并返回渲染的视图内容.
 * 支持布局管理，主题管理以及通过<i>WindViewTemplate</i>支持视图模板编译。
 * 组件定义:<code>
 * 'viewResolver' => array(
 * 'path' => 'WIND:viewer.WindViewerResolver',
 * 'scope' => 'prototype',
 * 'properties' => array(
 * 'windLayout' => array(
 * 'ref' => 'layout',
 * )))</code>
 * <note><b>注意:</b>框架默认视图渲染引擎组件,可以通过覆盖component相关配置进行修改</note>
 * <note>WindView和WindViewerResolver是相互配合使用的,等WindView接受一个视图渲染请求后会初始化一个ViewerResolver对象并将进一步的视图渲染工作移交给该对象.
 * 而ViewerResolver对象在进行视图渲染时的状态信息，模板信息，以及配置信息都来自于WindView对象.ViewerResolver对象中的WindView对象必须是创建ViewerResolver的那个对象.
 * 我们可以通过修改view的component配置来注入不同的ViewerResolver实现.
 * </note>
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.viewer
 */
class WindViewerResolver extends WindModule implements IWindViewerResolver {
	/**
	 * 存储视图输出变量
	 * 
	 * @var array
	 */
	protected $vars = array();
	/**
	 * 视图对象
	 * 
	 * 通过该对象获得相关视图配置信息
	 * @var WindView
	 */
	protected $windView = null;
	/**
	 * 视图布局对象
	 * 
	 * @var WindLayout
	 */
	protected $windLayout = null;

	/**
	 * @param WindView $windView 视图对象 默认值为null
	 */
	public function __construct($windView = null) {
		$this->windView = $windView;
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windFetch()
	 */
	public function windFetch($template = '') {
		ob_start();
		if (!$template) {
			$template = $this->windView->templateName;
			$templateFilePath = $this->windView->getViewTemplate($template);
			$compileFilePath = $this->windView->getCompileFile($template);
			if ($this->checkReCompile($templateFilePath, $compileFilePath)) {
				$layout = $this->getWindLayout();
				$layout->setLayout($this->windView->layout);
				$layout->setTheme($this->windView->theme);
				WindFile::write($compileFilePath, $layout->parser($this));
			}
		} else
			list($compileFilePath) = $this->compile($template);
		
		WindRender::render($compileFilePath, Wind::getApp()->getResponse()->getData($template), $this);
		return ob_get_clean();
	}

	/* (non-PHPdoc)
	 * @see IWindViewerResolver::windAssign()
	 */
	public function windAssign($vars, $key = '') {}

	/**
	 * 编译模板并返回编译后模板地址及内容
	 * 
	 * <pre>
	 * <i>$output==true</i>返回编译文件绝对路径地址和内容,不生成编译文件;
	 * <i>$output==false</i>返回编译文件绝对路径地址和内容,生成编译文件
	 * <pre>
	 * 
	 * @param string $template 模板名称 必填
	 * @param string $suffix 模板后缀 默认为空 
	 * @param boolean $output 是否直接输出模板内容,接受两个值true,false 默认值为false
	 * @return array(compileFile,content)<pre>
	 * <i>compileFile</i>模板编译文件绝对地址,
	 * <i>content</i>编译后模板输出内容,当<i>$output</i>
	 * 为false时将content写入compileFile</pre>
	 */
	public function compile($template, $suffix = '', $output = false) {
		$templateFile = $this->windView->getViewTemplate($template, $suffix);
		if (!is_file($templateFile)) {
			throw new WindViewException('[component.viewer.WindViewerResolver.compile] ' . $templateFile, WindViewException::VIEW_NOT_EXIST);
		}
		$compileFile = $this->windView->getCompileFile($template);
		if (!$this->checkReCompile($templateFile, $compileFile)) return array($compileFile, '');
		/* @var $_windTemplate WindViewTemplate */
		$_windTemplate = Wind::getApp()->getWindFactory()->getInstance('template');
		$_output = $_windTemplate->compile($templateFile, $this);
		if ($output === false) {
			WindFile::write($compileFile, $_output);
		}
		return array($compileFile, $_output);
	}

	/**
	 * 检查是否需要重新编译,需要编译返回false，不需要编译返回true
	 * 
	 * 是否重新进行编译取决于两个变量'WIND_DEBUG'和'isCompile','WIND_DEBUG'是框架层面的'DEBUG'控制常量,当'DEBUG'开启时则总是重新生成编译模板.
	 * 'isCompile'是一个配置值来自'WindView'对象,用户可以通过配置进行修改.当'isCompile'为'1'时,程序会进一步判断,当编译文件不存在或者已经过期时对模板进行重新编译.
	 * 如果'isCompile'为'0',则不对模板文件进行重新编译.
	 * 
	 * @param string $templateFilePath 模板路径
	 * @param string $compileFilePath 编译路径
	 * @return boolean
	 */
	private function checkReCompile($templateFilePath, $compileFilePath) {
		if (WIND_DEBUG) return true;
		if ($this->getWindView()->isCompile) {
			$_c_m_t = @filemtime($compileFilePath);
			if ((int) $_c_m_t <= (int) @filemtime($templateFilePath)) return true;
		}
		return false;
	}

	/**
	 * @return WindView
	 */
	public function getWindView() {
		return $this->windView;
	}

	/**
	 * @param WindView $windView
	 * @return void
	 */
	public function setWindView($windView) {
		$this->windView = $windView;
	}

	/**
	 * @return WindLayout
	 */
	public function getWindLayout() {
		return $this->_getWindLayout('', $this);
	}
}

/**
 * 辅助WindViewerResolver完成视图渲染工作
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.viewer
 */
class WindRender {

	/**
	 * 视图渲染
	 * 
	 * @param string $__tpl
	 * @param array $__vars
	 * @param WindViewerResolver $__viewer
	 * @return void
	 * @throws WindViewException
	 */
	public static function render($__tpl, $__vars, $__viewer) {
		$__theme = $__viewer->getWindView()->theme;
		$themeUrl = $__theme ? $__theme : Wind::getApp()->getRequest()->getBaseUrl(true);
		unset($__theme);
		@extract($__vars, EXTR_REFS);
		if (!@include_once ($__tpl)) throw new WindViewException('[component.viewer.WindRender.render] template name ' . $__tpl, WindViewException::VIEW_NOT_EXIST);
	}
}