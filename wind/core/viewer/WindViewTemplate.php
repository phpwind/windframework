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

	private $reCompile = '';

	public $left_delimiter = "{";

	public $right_delimiter = "}";

	/**
	 * 进行视图渲染
	 * 
	 * @param string $templateFile | 模板文件
	 * @param string $compileFile | 编译后生成的文件
	 * @param WindView $windView
	 */
	public function render($templateFile, $compileFile, $windView) {
		$_output = null;
		if (!$windView->getCompileDir()) return $_output;
		$this->checkReCompile($templateFile, $compileFile);
		if (!$this->reCompile) return $_output;
		$_output = $this->getTemplateFileContent($templateFile);
		//TODO compile template content
		

		L::import('WIND:component.utility.WindFile');
		WindFile::writeover($compileFile, $_output);
		
		return $_output;
	}

	/**
	 * 对模板内容进行编译
	 * 
	 * @param string $content
	 */
	private function compile($content) {
		//TODO 
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
			throw new WindViewException('Unable to open the template file.');
		return $_output;
	}

	/**
	 * 检查是否需要重新编译
	 * 
	 * @param unknown_type $templateFile
	 * @param unknown_type $compileFile
	 */
	private function checkReCompile($templateFile, $compileFile) {
		$this->reCompile = false;
		$compileFileModifyTime = @filemtime($compileFile);
		if ($compileFileModifyTime === false) {
			$this->reCompile = true;
			return;
		}
		$templateFileModifyTime = @filemtime($templateFile);
		if ((int) $templateFileModifyTime >= $compileFileModifyTime) {
			$this->reCompile = true;
		}
		return;
	}

}

?>