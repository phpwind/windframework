<?php

L::import('WIND:core.WindComponentModule');
/**
 * 模板类
 * 职责：进行模板编译渲染
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindViewTemplate extends WindComponentModule {

	const COMPILER_ECHO = 'WIND:core.viewer.compiler.WindTemplateCompilerEcho';

	const COMPILER_INTERNAL = 'WIND:core.viewer.compiler.WindTemplateCompilerInternal';

	const SUPPORT_TAGS = 'support-tags';

	const TAG = 'tag';

	const REGEX = 'regex';

	const COMPILER = 'compiler';

	protected $leftDelimiter = "<!--{";

	protected $rightDelimiter = "}-->";

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

	/**
	 * 进行视图渲染
	 * 
	 * @param string $templateFile | 模板文件
	 * @param string $compileFile | 编译后生成的文件
	 * @param WindView $windView
	 */
	public function render($templateFile, $compileFile, $windView) {
		if (!$windView->getCompileDir()) {
			throw new WindViewException('compile dir is not exist \'' . $windView->getCompileDir() . '\' .');
		}
		if (!$this->checkReCompile($templateFile, $compileFile)) return null;
		$_output = $this->getTemplateFileContent($templateFile);
		$_output = $this->compile($_output);
		$this->cacheCompileResult($compileFile, $_output);
		return $_output;
	}

	/**
	 * 对模板内容进行编译
	 * @param string $content
	 */
	protected function compile($content) {
		$content = str_replace(array($this->getLeftDelimiter(), $this->getRightDelimiter()), array('<?php', '?>'), $content);
		$content = $this->registerTags($content);
		if ($this->windHandlerInterceptorChain !== null) {
			$this->windHandlerInterceptorChain->getHandler()->handle();
		}
		foreach (array_reverse($this->getCompiledBlockData()) as $key => $value) {
			if (!$key) continue;
			$content = str_replace($this->getBlockTag($key), ($value ? $value : ' '), $content);
		}
		$content = preg_replace('/\?>(\s|\n)*?<\?php/i', '', $content);
		return $content;
	}

	/**
	 * 注册支持的标签并返回注册后的模板内容
	 * @param string $content
	 * @return string 
	 */
	private function registerTags($content) {
		$content = $this->creatTagCompiler($content, self::COMPILER_INTERNAL, '/<\?php(.|\n)*?\?>/i');
		$tags = $this->getConfig()->getConfig(self::SUPPORT_TAGS);
		foreach ((array) $tags as $key => $value) {
			$compiler = isset($value[self::COMPILER]) ? $value[self::COMPILER] : '';
			$tag = isset($value[self::TAG]) ? $value[self::TAG] : '';
			if (!$compiler || !$tag) continue;
			$regex = '/<(' . $tag . ')(\s|>)+(.)+?(\/>[^"\']|<\/\1>){1}/i';
			$content = $this->creatTagCompiler($content, $compiler, $regex);
		}
		$content = $this->creatTagCompiler($content, self::COMPILER_ECHO, '/{*(\s*\$\w+\s*)}*/i');
		return $content;
	}

	/**
	 * Enter description here ...
	 * @param content
	 * @param compiler
	 * @param regex
	 */
	private function creatTagCompiler($content, $compiler, $regex) {
		$content = preg_replace_callback($regex, array($this, 'registerCompiler'), $content);
		if ($this->windHandlerInterceptorChain === null) {
			L::import('WIND:core.filter.WindHandlerInterceptorChain');
			$this->windHandlerInterceptorChain = new WindHandlerInterceptorChain();
		}
		$_compilerClass = L::import($compiler);
		if (!class_exists($_compilerClass)) return $content;
		$this->windHandlerInterceptorChain->addInterceptors(new $_compilerClass($this->_compilerCache, $this));
		$this->_compilerCache = array();
		return $content;
	}

	/**
	 * 注册标签解析器
	 * @param string $content
	 */
	private function registerCompiler($content) {
		$_content = $content[0];
		if (!$_content) return '';
		
		$key = $this->getCompiledBlockKey();
		$this->_compilerCache[] = array($key, $_content);
		return $this->getBlockTag($key);
	}

	/**
	 * 获得模板文件内容，目前只支持本地文件获取
	 * 
	 * @param string $templateFile
	 */
	private function getTemplateFileContent($templateFile) {
		$_output = '';
		if ($fp = @fopen($templateFile, 'r')) {
			while (!feof($fp)) {
				$_output .= fgets($fp, 4096);
			}
			fclose($fp);
		} else
			throw new WindViewException('Unable to open the template file \'' . $templateFile . '\'.');
		
		return $_output;
	}

	/**
	 * 检查是否需要重新编译
	 * 
	 * @param string $templateFile
	 * @param string $compileFile
	 */
	private function checkReCompile($templateFile, $compileFile) {
		$_reCompile = false;
		if (IS_DEBUG) {
			$_reCompile = true;
		} elseif (false === ($compileFileModifyTime = @filemtime($compileFile))) {
			$_reCompile = true;
		} else {
			$templateFileModifyTime = @filemtime($templateFile);
			if ((int) $templateFileModifyTime >= $compileFileModifyTime) $_reCompile = true;
		}
		return $_reCompile;
	}

	/**
	 * 将编译结果进行缓存
	 * @param string $compileFile | 编译缓存文件
	 * @param string $content | 模板内容
	 */
	private function cacheCompileResult($compileFile, $content) {
		if (!$compileFile || !file_exists($compileFile) || empty($content)) return;
		L::import('WIND:component.utility.WindFile');
		WindFile::writeover($compileFile, $content);
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
		L::import('WIND:component.utility.WindUtility');
		$key = WindUtility::generateRandStr(50);
		if (key_exists($key, $this->compiledBlockData)) {
			return $this->getCompiledBlockKey();
		}
		return $key;
	}

	/**
	 * 返回左侧定界符，去掉两侧空白符
	 * @return string $leftDelimiter
	 */
	public function getLeftDelimiter() {
		$this->leftDelimiter = trim($this->leftDelimiter);
		return $this->leftDelimiter;
	}

	/**
	 * 返回右侧定界符，去掉两侧空白符
	 * @return string $rightDelimiter
	 */
	public function getRightDelimiter() {
		return $this->rightDelimiter;
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