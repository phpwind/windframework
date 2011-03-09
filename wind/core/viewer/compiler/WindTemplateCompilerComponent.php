<?php

L::import('WIND:core.viewer.AbstractWindTemplateCompiler');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerComponent extends AbstractWindTemplateCompiler {

	protected $name = '';

	protected $app = '';

	protected $args = '';

	protected $tpl = '';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$_output .= $this->getScript() . '<iframe src="http://t4.pw.com/comment/index.php?commonid=1&topicid=1" frameborder="0" scrolling="auto"></iframe>';
		return $_output;
	}

	/**
	 * @return string
	 */
	private function getScript() {
		$_tmp = '';
		
		return $_tmp;
	}

	/**
	 * @return string
	 */
	private function getTplContent() {
		
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	protected function getProperties() {
		return array('name', 'app', 'args', 'tpl');
	}
}

?>