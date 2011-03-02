<?php

L::import('WIND:core.viewer.AbstractWindTemplateCompiler');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerTemplate extends AbstractWindTemplateCompiler {

	protected $source = '';

	protected $suffix = '';

	protected $load = 'true';

	protected $includeFiles = '';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if (!isset($this->source)) return $content;
		preg_match('/[\$\(\/\\]/i', $this->source, $result);
		if (empty($result)) {
			if ($this->load === 'false') {
				$content = '<?php include(\'' . addslashes($this->windViewerResolver->compile($this->source, $this->suffix)) . '\'); ?>';
			} else {
				list($compileFile, $content) = $this->windViewerResolver->compile($this->source, $this->suffix, true);
				
				$this->includeFiles .= $this->source . ':' . filemtime($compileFile) . ' ';
			}
		} else
			$content = '<?php include(' . $this->source . '); ?>';
		
		return $content;
	}

	protected function postCompile() {

	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('source', 'suffix', 'load');
	}

}

?>