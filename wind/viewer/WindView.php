<?php
Wind::import('WIND:utility.WindFolder');
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
	protected $theme = array('theme' => '', 'package' => '');
	/**
	 * 视图解析引擎,通过组件配置改变该类型
	 * 
	 * @var WindViewerResolver
	 */
	protected $viewResolver = null;
	/**
	 * 视图布局管理器
	 *
	 * @var WindLayout
	 */
	protected $windLayout = null;

	/* (non-PHPdoc)
	 * @see IWindView::render()
	 */
	public function render($display = false) {
		if (!$this->templateName) return;
		
		/* @var $viewResolver WindViewerResolver */
		$viewResolver = $this->_getViewResolver();
		$viewResolver->setWindView($this);
		if ($viewResolver === null) throw new WindException(
			'[view.WindView.render] View renderer initialization failure.');
		$viewResolver->windAssign(Wind::getApp()->getResponse()->getData($this->templateName));
		if ($display === false) {
			if ($this->layout) {
				/* @var $layout WindLayout */
				$layout = $this->_getWindLayout();
				$content = $layout->parser($this->layout, $viewResolver);
			} else
				$content = $viewResolver->windFetch();
			Wind::getApp()->getResponse()->setBody($content, $this->templateName);
		} else {
			echo $viewResolver->windFetch();
		}
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
			$this->htmlspecialchars = $this->getConfig('htmlspecialchars', '', 
				$this->htmlspecialchars);
			$this->setThemePackage($this->getConfig('theme-package'));
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
		if (!empty($this->theme['package']) && !empty($this->theme['theme'])) {
			list(, $_template) = explode(':', $template, 2);
			$_template = $this->theme['package'] . '.' . $this->theme['theme'] . '.' . $_template;
			$realPath = Wind::getRealPath($_template, ($ext ? $ext : false), true);
			if (is_file($realPath)) return $realPath;
		}
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
		if (!$template) $template = $this->templateName;
		if (false !== ($pos = strpos($template, ':'))) {
			$template = str_replace('.', '_', '__external.' . substr($template, $pos + 1));
		}
		if (!empty($this->theme['theme']) && !empty($this->theme['package'])) {
			$template = $this->compileDir . '.' . $this->theme['theme'] . '.' . $template;
		} else {
			$template = $this->compileDir . '.' . $template;
		}
		$dir = Wind::getRealPath($template, false, true);
		WindFolder::mkRecur(dirname($dir));
		return $this->compileExt ? $dir . '.' . $this->compileExt : $dir;
	}

	/**
	 * 设置当前主题包的位置
	 *
	 * @param string $package
	 */
	public function setThemePackage($package) {
		$this->theme['package'] = $package;
	}

	/**
	 * @param string $theme
	 */
	public function setTheme($theme) {
		$this->theme['theme'] = $theme;
	}

}