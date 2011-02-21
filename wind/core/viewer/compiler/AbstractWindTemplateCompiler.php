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

	protected $tagContent = '';

	protected $windViewTemplate = null;

	protected $tagKey = '';

	/**
	 * 初始化标签解析器
	 * 
	 * @param string $tagContent
	 * @param WindViewTemplate $windViewTemplate
	 */
	public function __construct($tagContent, $key, $windViewTemplate) {
		$this->tagContent = $tagContent;
		$this->tagKey = $key;
		$this->windViewTemplate = $windViewTemplate;
	}

	/**
	 * 模板编译方法
	 * @param string $content | 模板内容
	 * @param WindViewTemplate $windViewTemplate | 模板编译引擎
	 * @return string | 输出编译后结果
	 */
	abstract public function compile();

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		if ($this->windViewTemplate === null) return;
		$_output = $this->compile();
		$this->windViewTemplate->setCompiledBlockData($this->tagKey, $_output);
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

}

?>