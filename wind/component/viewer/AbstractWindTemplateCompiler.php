<?php
/**
 * 视图解析,抽象接口
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindTemplateCompiler extends WindHandlerInterceptor {
	
	protected $tags = array();
	
	/**
	 * @var WindViewTemplate
	 */
	protected $windViewTemplate = null;
	
	/**
	 * @var WindViewerResolver
	 */
	protected $windViewerResolver = null;
	
	protected $request = null;
	
	protected $response = null;

	/**
	 * 初始化标签解析器
	 * @param string $tagContent
	 * @param WindViewTemplate $windViewTemplate
	 * @param WindViewerResolver $windViewerResolver
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct($tags, $windViewTemplate, $windViewerResolver, $request, $response) {
		$this->tags = $tags;
		$this->windViewTemplate = $windViewTemplate;
		$this->windViewerResolver = $windViewerResolver;
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * 模板编译方法
	 * @param string $content | 模板内容
	 * @param WindViewTemplate $windViewTemplate | 模板编译引擎
	 * @return string | 输出编译后结果
	 */
	abstract public function compile($key, $content);

	/**
	 * 编译前预处理
	 */
	protected function preCompile() {}

	/**
	 * 编译后处理结果
	 */
	protected function postCompile() {}

	/**
	 * 返回该标签支持的属性信息
	 */
	protected function getProperties() {
		return array();
	}

	/**
	 * 解析属性值
	 * 
	 * @param string $content
	 */
	protected function compileProperty($content) {
		foreach ($this->getProperties() as $value) {
			if (!$value) continue;
			preg_match('/(' . preg_quote($value) . '\s*=\s*([\'\"])?)[^\'\"\s]*(?=(\2)?)/i', $content, $result);
			if ($result) $this->$value = trim(str_replace($result[1], '', $result[0]));
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		if ($this->windViewTemplate === null) return;
		$this->preCompile();
		foreach ($this->tags as $key => $value) {
			if (!$value[0] || !$value[1]) continue;
			$this->compileProperty($value[1]);
			$_output = $this->compile($value[0], $value[1]);
			$this->windViewTemplate->setCompiledBlockData($value[0], $_output);
		}
		$this->postCompile();
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {}

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

	/**
	 * @return WindViewTemplate
	 */
	protected function getWindViewTemplate() {
		return $this->windViewTemplate;
	}

}

?>