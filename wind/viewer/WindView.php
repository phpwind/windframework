<?php
Wind::import('WIND:viewer.IWindView');
/**
 * 视图处理器
 * 
 * <i>WindView</i>是基础的视图处理器,职责：进行视图渲染.<br>
 * 他实现自接口<i>IWindView</i>,该类依赖<i>WindViewerResolver</i>完成视图渲染工作<br/>
 * <i>WindView</i>支持丰富的配置信息，可以通过修改相关配置来改变视图输出行为.<i>template-dir</i>
 * :模板目录,支持命名空间格式:WIND:template,当命名空间为空时以当前app的rootpath为默认命名空间;<i>template-ext</i>
 * :模板后缀,默认为htm,可以通过配置该值来改变模板的后缀名称;<i>is-compile</i>
 * :是否开启模板自动编译,当开启自动编译时程序会根据编译文件是否存在或者是否已经过期来判断是否需要进行重新编译.支持'0'和'1'两种输入,默认值为'0'.<i>compile-dir</i>
 * :模板编译目录,输入规则同'template-dir'.(注意:该目录需要可写权限).
 * 默认配置支持如下：<code> array(
 * 'template-dir' => 'template',
 * 'template-ext' => 'htm',
 * 'is-compile' => '0',
 * 'compile-dir' => 'DATA:template',
 * 'compile-ext' => 'tpl', //模板后缀
 * 'layout' => '', //布局文件
 * 'theme' => '', //主题包位置
 * 'htmlspecialchars' => 'true', //是否开启对输出模板变量进行过滤
 * )
 * </code>
 * 该类的组件配置格式：<code>
 * 'windView' => array('path' => 'WIND:viewer.WindView',
 * 'scope' => 'prototype',	//注意:命名空间为'prototype'
 * 'config' => array(
 * 'template-dir' => 'template',
 * 'template-ext' => 'htm',
 * 'is-compile' => '0',
 * 'compile-dir' => 'compile.template',
 * 'compile-ext' => 'tpl',
 * 'layout' => '',
 * 'theme' => ''),
 * 'properties' => array(
 * 'viewResolver' => array('ref' => 'viewResolver')
 * ))</code>
 * <note><b>注意:</b>框架默认视图组件,通过修改组件配置修改默认视图组件.(详细操作参考组件配置定义)</note>
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license {@link http://www.windframework.com}
 * @version $Id$
 * @package viewer
 */
class WindView extends WindModule implements IWindView {
	/**
	 * 模板目录
	 * 
	 * 支持命名空间格式:<i>WIND:template</i>,
	 * 当命名空间为空时以当前<i>app</i>的<i>rootpath</i>为默认命名空间
	 * @var string
	 */
	public $templateDir;
	/**
	 * 模板文件的扩展名
	 * 
	 * @var string
	 */
	public $templateExt;
	/**
	 * 模板名称
	 * 
	 * @var string
	 */
	public $templateName;
	/**
	 * 是否对模板变量进行html字符过滤
	 * 
	 * @var boolean
	 */
	public $htmlspecialchars = true;
	/**
	 * 是否开启模板自动编译
	 * 
	 * 接受两种输入值0和1<ol>
	 * <li>0   关闭,不进行模板编译</li>
	 * <li>1  进行模板编译</li></ol>
	 * @var int
	 */
	public $isCompile = 0;
	/**
	 * 模板编译文件生成目录,目录定义规则同<i>templateDir</i>
	 * 
	 * @var string
	 */
	public $compileDir;
	/**
	 * 模板编译文件生成后缀,默认值为'tpl'
	 * 
	 * @var string
	 */
	public $compileExt = 'tpl';
	/**
	 * 模板布局文件
	 * 
	 * @var string
	 */
	public $layout;
	/**
	 * 主题包目录
	 * 
	 * @var string
	 */
	public $theme;
	/**
	 * 视图解析引擎,通过组件配置改变该类型
	 * 
	 * @var WindViewerResolver
	 */
	protected $viewResolver = null;
	
	/**
	 * @var WindXmlParser
	 */
	protected $xmlParser = null;

	/* (non-PHPdoc)
	 * @see IWindView::render()
	 */
	public function render($display = false) {
		if (!$this->templateName) return;
		$_type = $this->getRequest()->getIsAjaxRequest() ? 'json' : $this->getResponse()->getResponseType();
		switch (strtolower($_type)) {
			case 'json':
				$this->renderWithJson();
				break;
			case 'ajax':
				$this->renderWithJson();
				break;
			case 'xml':
				$this->renderWithXml();
				break;
			default:
				$this->_render($display);
		}
	}

	/**
	 * html格式视图渲染方法
	 *
	 * @param boolean $display
	 * @return void
	 */
	protected function _render($display) {
		$viewResolver = $this->_getViewResolver();
		$viewResolver->setWindView($this);
		$viewResolver->windAssign(Wind::getApp()->getResponse()->getData($this->templateName));
		if ($display === false) {
			$this->getResponse()->setBody($viewResolver->windFetch(), $this->templateName);
		} else {
			echo $viewResolver->windFetch();
		}
	}

	/**
	 * xml格式数据输出渲染
	 */
	protected function renderWithXml() {
		$this->getResponse()->setHeader('Content-type', 'text/xml; charset=utf-8');
		$_vars = $this->getResponse()->getData($this->templateName);
		$_vars['G'] = $this->getResponse()->getData('G');
		$parser = $this->_getXmlParser();
		if ($parser === null) {
			Wind::import("WIND:parser.WindXmlParser");
			$parser = new WindXmlParser();
		}
		echo $parser->parseToXml($_vars);
	}

	/**
	 * json格式视图输出数据渲染
	 */
	protected function renderWithJson() {
		$this->getResponse()->setHeader('Content-type', 'application/json; charset=utf-8');
		$_vars = $this->getResponse()->getData($this->templateName);
		$_vars['G'] = $this->getResponse()->getData('G');
		echo WindJson::encode($_vars);
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($this->_config) {
			$this->templateDir = $this->getConfig('template-dir', '', $this->templateDir);
			$this->templateExt = $this->getConfig('template-ext', '', $this->templateExt);
			$this->compileDir = $this->getConfig('compile-dir', '', $this->compileDir);
			$this->compileExt = $this->getConfig('compile-ext', '', $this->compileExt);
			$this->isCompile = $this->getConfig('is-compile', '', $this->isCompile);
			$this->layout = $this->getConfig('layout', '', $this->layout);
			$this->theme = $this->getConfig('theme', '', $this->theme);
			$this->htmlspecialchars = $this->getConfig('htmlspecialchars', '', $this->htmlspecialchars);
		}
	}

	/**
	 * 返回模板绝对路径信息
	 * 
	 * 根据模板的逻辑名称,返回模板的绝对路径信息,支持命名空间方式定义模板信息.<code>
	 * $template='templateName'; //return $templateDir/templateName.$ext
	 * $template='subTemplateDir.templateName'; //return $templateDir/subTemplateDir/templateName.$ext
	 * $template='namespace:templateName'; //return namespace:templateName.$ext</code>
	 * <note><b>注意:</b>$template为空则返回当前的模板的路径信息.模板文件后缀名可以通过修改配置进行修改.</note>
	 * @param string $template 模板名称, 默认值为空 , 为空则返回当前模板的绝对地址
	 * @param string $ext 模板后缀, 默认值为空, 为空则返回使用默认的后缀
	 * @return string
	 */
	public function getViewTemplate($template = '', $ext = '') {
		!$template && $template = $this->templateName;
		!$ext && $ext = $this->templateExt;
		if (false === strpos($template, ':')) $template = $this->templateDir . '.' . $template;
		return Wind::getRealPath($template, ($ext ? $ext : false), true);
	}

	/**
	 * 返回模板的编译文件绝对路径地址
	 * 
	 * 根据模板的逻辑名称,返回模板的绝对路径信息,支持命名空间方式定义模板信息.<code>
	 * $template='templateName'; //return $compileDir/templateName.$ext
	 * $template='subTemplateDir.templateName'; //return $compileDir/subTemplateDir_templateName.$ext
	 * $template='namespace:templateName'; //return $compileDir/__external_subDir_templateName.$ext</code>
	 * <note><b>注意:</b>$template为空则返回当前的模板的路径信息.</note>
	 * @param string $template 模板名称, 默认值为空, 为空则返回当前模板的编译文件
	 * @return string
	 */
	public function getCompileFile($template = '') {
		if (!$this->compileDir) return;
		if ($this->compileDir == $this->templateDir) throw new WindViewException(
			'[wind.viewer.WindView.getCompileFile] the same directory compile and template.');
		if (!$template)
			$template = $this->templateName;
		elseif (false !== ($pos = strpos($template, ':')))
			$template = '__external.' . substr($template, $pos + 1);
		
		$_dirs = explode('.', $this->compileDir . '.');
		$dir = realpath(Wind::getRealPath($_dirs[0], false, true));
		if (!is_dir($dir)) throw new WindViewException(
			'[viewer.WindView.getCompileFile] Template compile dir ' . $this->compileDir . ' is not exist.');
		unset($_dirs[0]);
		foreach ($_dirs as $_sub) {
			if (!$_sub) continue;
			$dir .= '/' . $_sub;
			if (!is_dir($dir)) mkdir($dir, '777');
		}
		$dir .= '/' . str_replace('.', '_', $template);
		return $this->compileExt ? $dir . '.' . $this->compileExt : $dir;
	}
}