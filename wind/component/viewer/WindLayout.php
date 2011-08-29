<?php
/**
 * 布局对象，
 * 通过加载一个布局对象，或者布局配置文件，或者设置布局变量来实现页面布局
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindLayout extends WindModule {
	/* 编译结果缓存 */
	protected $blockKey = "<pw-wind key='\$' />";
	/**
	 * 定义主题包的位置
	 * @var string
	 */
	private $theme;
	/**
	 * javascript集合
	 * @var string
	 */
	private $script;
	/**
	 * css 集合
	 * @var string
	 */
	private $css;
	/**
	 * 布局文件的位置
	 * @var string
	 */
	private $layout;
	/**
	 * @var array
	 */
	private $segments = array();
	/**
	 * @var WindViewerResolver
	 */
	private $viewer = null;

	/**
	 * @param string $layoutFile
	 */
	public function __construct($layoutFile = '') {
		$this->setLayoutFile($layoutFile);
	}

	/**
	 * 解析布局文件
	 * @param WindViewerResolver $viewer
	 */
	public function parser($viewer) {
		$this->viewer = $viewer;
		$content = '';
		ob_start();
		if ($this->layout) {
			list($tpl) = $this->viewer->compile($this->layout);
			if (!@include ($tpl)) {
				throw new WindViewException('[component.viewer.WindLayout.parser] layout file ' . $tpl, 
					WindViewException::VIEW_NOT_EXIST);
			}
		} else
			$this->content();
		$content = ob_get_clean();
		foreach ($this->segments as $key => $value) {
			if ($key)
				$content = str_replace("<pw-wind key='" . $key . "' />", $value[1], $content);
		}
		$content = preg_replace('/(<\/body>)/i', $this->script . '\\1', $content);
		//$content = preg_replace('/<\/head>/i', $this->css . '</head>', $content);
		return $content;
	}

	/**
	 * 设置切片内容
	 * @param string $template
	 */
	public function segment($template) {
		$this->segments[$template] = $this->viewer->compile($template, '', true);
		echo "<pw-wind key='" . $template . "' />";
	}

	/**
	 * 当前模板内容
	 * @param string $template
	 */
	public function content() {
		$template = $this->viewer->getWindView()->templateName;
		$this->segment($template);
	}

	/**
	 * @param string $theme
	 */
	public function setTheme($theme) {
		$this->theme = $theme;
	}

	/**
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * @param string $script
	 */
	public function setScript($script) {
		$this->script .= $script;
	}

	/**
	 * @param string $css
	 */
	public function setCss($css) {
		$this->css .= $css;
	}

}