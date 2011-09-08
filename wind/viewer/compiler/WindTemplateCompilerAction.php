<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * <doAction /> 标签解析脚本
 * 支持属性: action\controller
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerAction extends AbstractWindTemplateCompiler {
	protected $action = '';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$content = '<?php $_tpl_forward = Wind::getApp()->getComponent(\'forward\');';
		$content .= '$_tpl_forward->forwardAction(\'' . $this->action . '\');';
		$content .= 'Wind::getApp()->doDispatch($_tpl_forward, true); ?>';
		return $content;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('action');
	}

}