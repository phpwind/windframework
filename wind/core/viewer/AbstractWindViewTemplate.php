<?php

L::import('WIND:core.WindComponentModule');
L::import('WIND:component.utility.WindFile');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindViewTemplate extends WindComponentModule {

	const SUPPORT_TAGS = 'support-tags';

	const TAG = 'tag';

	const REGEX = 'regex';

	const COMPILER = 'compiler';

	const PATTERN = 'pattern';

	/**
	 * 对模板内容进行编译
	 * @param string $content
	 * @param WindViewerResolver $windViewerResolver
	 */
	abstract protected function doCompile($content, $windViewerResolver = null);

	/**
	 * 进行视图渲染
	 * @param string $templateFile | 模板文件
	 * @param string $compileFile | 编译后生成的文件
	 * @param WindViewerResolver $windViewerResolver
	 */
	public function compile($templateFile, $compileFile, $windViewerResolver) {
		if (!$this->checkReCompile($templateFile, $compileFile)) return null;
		$_output = $this->getTemplateFileContent($templateFile);
		$_output = $this->compileDelimiter($_output);
		$_output = $this->doCompile($_output, $windViewerResolver);
		$this->cacheCompileResult($compileFile, $_output);
		return $_output;
	}

	/**
	 * @param string content
	 * @return string $content
	 */
	protected function compileDelimiter($content) {
		$content = str_replace(array('<!--{', '<!--#'), '<?php ', $content);
		$content = str_replace(array('}-->', '#-->'), '?>', $content);
		return $content;
	}

	/**
	 * 获得模板文件内容，目前只支持本地文件获取
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
	 * 将编译结果进行缓存
	 * @param string $compileFile | 编译缓存文件
	 * @param string $content | 模板内容
	 */
	private function cacheCompileResult($compileFile, $content) {
		if (!$compileFile) return;
		WindFile::write($compileFile, $content);
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
	 * 返回模板支持的标签
	 * array('tagName'=>array('tag','compiler'))
	 * @return array
	 */
	protected function getTags() {
		return $this->getConfig()->getConfig(self::SUPPORT_TAGS);
	}
}

?>