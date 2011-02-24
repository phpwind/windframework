<?php

L::import('WIND:core.filter.WindHandlerInterceptor');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindTemplateCompiler extends WindHandlerInterceptor {

	protected $tags = array();

	protected $windViewTemplate = null;

	/**
	 * 初始化标签解析器
	 * 
	 * @param string $tagContent
	 * @param WindViewTemplate $windViewTemplate
	 */
	public function __construct($tags, $windViewTemplate) {
		$this->tags = $tags;
		$this->windViewTemplate = $windViewTemplate;
	}

	/**
	 * 模板编译方法
	 * @param string $content | 模板内容
	 * @param WindViewTemplate $windViewTemplate | 模板编译引擎
	 * @return string | 输出编译后结果
	 */
	abstract public function compile($key, $content);

	/**
	 * 解析属性值
	 * @param string $content
	 */
	protected function compileProperty($content) {
		foreach ($this->getProperties() as $value) {
			if (!$value) continue;
			preg_match('/(' . $value . '\s*=\s*[\'\"\s]*)([^\'\"\s])+?(?=[\'\"\s])/i', $content, $result);
			$this->$value = trim(str_replace($result[1], '', $result[0]));
		}
	}

	/**
	 * 解析标签体
	 * @param string $content
	 */
	protected function compileContent($content) {

	}

	/**
	 * 返回该标签支持的属性信息
	 */
	protected function getProperties() {
		return array();
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		if ($this->windViewTemplate === null) return;
		foreach ($this->tags as $key => $value) {
			if (!$value[0] || !$value[1]) continue;
			$this->compileProperty($value[1]);
			$_output = $this->compile($value[0], $value[1]);
			$this->windViewTemplate->setCompiledBlockData($value[0], $_output);
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::handle()
	 */
	public function handle() {
		$args = func_get_args();
		call_user_func_array(array($this, 'preHandle'), $args);
		if (null !== ($handler = $this->interceptorChain->getHandler())) {
			call_user_func_array(array($handler, 'handle'), $args);
		}
		call_user_func_array(array($this, 'postHandle'), $args);
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {}

	/**
	 * @return WindViewTemplate $windViewTemplate
	 */
	protected function getWindViewTemplate() {
		return $this->windViewTemplate;
	}

}

?>