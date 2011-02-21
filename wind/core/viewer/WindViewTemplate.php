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

	protected $leftDelimiter = "<!--{";

	protected $rightDelimiter = "}-->";

	/* 编译结果缓存 */
	protected $blockKey = "<pw-wind key='$' />";

	protected $compiledBlockData = array();

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
	private function compile($content) {
		$content = str_replace(array($this->getLeftDelimiter(), $this->getRightDelimiter()), array('<?php', '?>'), $content);
		$content = preg_replace('/\?>(\s|\n)*?<\?php/i', '', $content);
		$content = preg_replace_callback('/<\?php(.|\n)*?\?>/', array($this, 'doCompileInternal'), $content);
		
		foreach ((array) $this->compiledBlockData as $key => $value) {
			$content = str_replace($this->getBlockKey($key), '<?php' . $value . '?>', $content);
		}
		return $content;
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
		if (false === ($compileFileModifyTime = @filemtime($compileFile)))
			$_reCompile = true;
		else {
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
		L::import('WIND:component.utility.WindFile');
		WindFile::writeover($compileFile, $content);
	}

	/**
	 * 处理匹配到的脚本定界符内部的处理脚本，并进行编译处理
	 * @param string $content
	 */
	private function doCompileInternal($content) {
		$_content = $content[0];
		$_content = str_replace(array('<?php', '?>'), array('', ''), $_content);
		return $this->saveCompileBlock($_content);
	}

	/**
	 * 处理匹配到的脚本定界符外部的处理脚本，并进行编译处理
	 * @param string $content
	 */
	private function doCompileExternal($content) {
		$_content = $content[0];
		
		return $_content;
	}

	/**
	 * 将编译好的内容缓存在变量中
	 * @param string $content
	 */
	private function saveCompileBlock($content) {
		L::import('WIND:component.utility.WindUtility');
		$key = WindUtility::generateRandStr(50);
		if (key_exists($key, $this->compiledBlockData)) {
			return $this->saveCompileBlock($content);
		}
		$this->compiledBlockData[$key] = $content;
		return $this->getBlockKey($key);
	}

	/**
	 * 获得块存储变量值
	 * @param string $key
	 * @return string|mixed
	 */
	private function getBlockKey($key) {
		if (!$this->blockKey) return '<pw-wind key=\'' . $key . '\' />';
		return str_replace('$', $key, $this->blockKey);
	}

	/**
	 * @return the $leftDelimiter
	 */
	protected function getLeftDelimiter() {
		$this->leftDelimiter = trim($this->leftDelimiter);
		return $this->leftDelimiter;
	}

	/**
	 * @return the $rightDelimiter
	 */
	protected function getRightDelimiter() {
		return $this->rightDelimiter;
	}

}

?>