<?php

L::import('WIND:core.viewer.AbstractWindViewTemplate');
/**
 * 模板类
 * 职责：进行模板编译渲染
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindViewTemplate extends AbstractWindViewTemplate {

	const COMPILER_ECHO = 'WIND:core.viewer.compiler.WindTemplateCompilerEcho';

	/* 编译结果缓存 */
	protected $blockKey = "<pw-wind key='$' />";

	protected $compiledBlockData = array();

	/**
	 * 模板编译器支持的标签信息
	 *
	 * @var array('targName','args info')
	 */
	protected $_compilerCache = array();

	protected $windHandlerInterceptorChain = null;

	/* (non-PHPdoc)
	 * @see AbstractWindViewTemplate::doCompile()
	 */
	protected function doCompile($content, $windViewerResolver = null) {
		$content = str_replace(array($this->getLeftDelimiter(), $this->getRightDelimiter()), array('<?php ', '?>'), $content);
		$content = $this->registerTags($content, $windViewerResolver);
		if ($this->windHandlerInterceptorChain !== null) {
			$this->windHandlerInterceptorChain->getHandler()->handle();
		}
		foreach (array_reverse($this->compiledBlockData) as $key => $value) {
			if (!$key) continue;
			$content = str_replace($this->getBlockTag($key), ($value ? $value : ' '), $content);
		}
		/*$content = preg_replace('/\?>(\s|\n)*?<\?php/i', '', $content);*/
		return $content;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindViewTemplate::getTags()
	 */
	protected function getTags() {
		$_tmp = '/<\?php(.|\n)*?\?>/i';
		$_tags['internal'] = $this->createTag('internal', 'WIND:core.viewer.compiler.WindTemplateCompilerInternal', $_tmp);
		$_tags['template'] = $this->createTag('template', 'WIND:core.viewer.compiler.WindTemplateCompilerTemplate');
		$_tags['page'] = $this->createTag('page', 'WIND:core.viewer.compiler.WindTemplateCompilerPage');
		$_tags += (array) parent::getTags();
		$_tmp = '/(\$|{\@\s*|{\s*\$|{\@\s*\$)\w+((\-\>\w+)*(\(([^\)])*\))*(\[([^\]])*\])*)*[\s;]*}*/i';
		$_tags['echo'] = $this->createTag('echo', 'WIND:core.viewer.compiler.WindTemplateCompilerEcho', $_tmp);
		return $_tags;
	}

	/**
	 * 创建tag配置
	 * @param string $tag
	 * @param string $class
	 */
	private function createTag($tag, $class, $pattern = '') {
		return array(self::TAG => $tag, self::PATTERN => $pattern, self::COMPILER => $class);
	}

	/**
	 * 注册支持的标签并返回注册后的模板内容
	 * @param string $content
	 * @param WindViewerResolver $windViewerResolver
	 * @return string 
	 */
	private function registerTags($content, $windViewerResolver = null) {
		foreach ((array) $this->getTags() as $key => $value) {
			$compiler = isset($value[self::COMPILER]) ? $value[self::COMPILER] : '';
			$regex = isset($value[self::PATTERN]) ? $value[self::PATTERN] : '';
			$tag = isset($value[self::TAG]) ? $value[self::TAG] : '';
			if (!$compiler || !$tag) continue;
			if ($regex === '') $regex = '/<(' . preg_quote($tag) . ')[^<>\n]*(\/>|>[^<>]*<\/\1>)/i';
			$content = $this->creatTagCompiler($content, $compiler, $regex, $windViewerResolver);
		}
		return $content;
	}

	/**
	 * 创建标签解析器类
	 * @param string content | 模板内容
	 * @param string compiler | 标签编译器
	 * @param string regex	| 正则表达式
	 * @param WindViewerResolver $windViewerResolver
	 */
	private function creatTagCompiler($content, $compiler, $regex, $windViewerResolver = null) {
		$content = preg_replace_callback($regex, array($this, '_creatTagCompiler'), $content);
		if ($this->windHandlerInterceptorChain === null) {
			L::import('WIND:core.filter.WindHandlerInterceptorChain');
			$this->windHandlerInterceptorChain = new WindHandlerInterceptorChain();
		}
		$_compilerClass = L::import($compiler);
		if (!class_exists($_compilerClass)) return $content;
		$this->windHandlerInterceptorChain->addInterceptors(new $_compilerClass($this->_compilerCache, $this, $windViewerResolver, $this->request, $this->response));
		$this->_compilerCache = array();
		return $content;
	}

	/**
	 * 将标签匹配到的模板内容设置到缓存中，并返回标识位到模板中进行内容站位
	 * @param string $content
	 * @return string|Ambigous --><string, mixed>
	 */
	private function _creatTagCompiler($content) {
		$_content = $content[0];
		if (!$_content) return '';
		
		$key = $this->getCompiledBlockKey();
		$this->_compilerCache[] = array($key, $_content);
		return $this->getBlockTag($key);
	}

	/**
	 * 对模板块存储进行标签处理
	 * 将Key串 'HhQWFLtU0LSA3nLPLHHXMtTP3EfMtN3FsxLOR1nfYC5OiZTQri' 处理为
	 * <pw-wind key='HhQWFLtU0LSA3nLPLHHXMtTP3EfMtN3FsxLOR1nfYC5OiZTQri' />
	 * 在模板中进行位置标识
	 * 
	 * @param string $key | 索引
	 * @return string|mixed | 处理后结果
	 */
	private function getBlockTag($key) {
		if (!$this->blockKey) return '<pw-wind key=\'' . $key . '\' />';
		return str_replace('$', $key, $this->blockKey);
	}

	/**
	 * 获得切分后块编译缓存Key值,Key值为一个50位的随机字符串,当产生重复串时继续查找
	 * @return string
	 */
	protected function getCompiledBlockKey() {
		$key = WindUtility::generateRandStr(50);
		if (key_exists($key, $this->compiledBlockData)) {
			return $this->getCompiledBlockKey();
		}
		return $key;
	}

	/**
	 * 返回编译后结果，根据Key值检索编译后结果，并返回
	 * @param string $key
	 * @return string
	 */
	public function getCompiledBlockData($key = '') {
		if ($key)
			return isset($this->compiledBlockData[$key]) ? $this->compiledBlockData[$key] : '';
		else
			return $this->compiledBlockData;
	}

	/**
	 * 根据key值保存编译后的模板块
	 * @param string $key | 索引
	 * @param string $compiledBlockData | 编译结果
	 * @param boolean $isTag | 再结果处理时是否添加php脚本定界符 true 添加 ，flase 不添加
	 */
	public function setCompiledBlockData($key, $compiledBlockData) {
		if ($key) $this->compiledBlockData[$key] = $compiledBlockData;
	}

}

?>