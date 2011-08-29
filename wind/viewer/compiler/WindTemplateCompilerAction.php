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
		return '<?php $_tpl_forward = Wind::getApp()->getComponent(\'forward\');
					$_tpl_forward->forwardAction(\'' . $this->action . '\');
					Wind::getApp()->doDispatch($_tpl_forward, true); ?>';
	}

	/**
	 * @return string
	 */
	public function getScript() {
		$_tmp = '$_tpl_forward = $this->getSystemFactory()->getInstance(COMPONENT_FORWARD);' . '$_tpl_forward->setDisplay(true);' . '$_tpl_forward->forwardAnotherAction(\'' . $this->action . '\', \'' . $this->controller . '\');' . '$_tpl_app = $this->getSystemFactory()->getInstance(COMPONENT_WEBAPP);' . '$_tpl_app->doDispatch($_tpl_forward);';
		return $_tmp;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('action');
	}

}