<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

L::import('WIND:core.viewer.AbstractWindTemplateCompiler');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerAction extends AbstractWindTemplateCompiler {

	protected $action = '';

	protected $controller = '';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$_content = '<?php ' . $this->getScript() . ' ?>' . "\r\n";
		return $_content;
	}

	/**
	 * @return string
	 */
	public function getScript() {
		$_tmp = '';
		$_tmp .= '$_tpl_forward = $this->windFactory->getInstance(COMPONENT_FORWARD);'.
		'$_tpl_forward->forwardAnotherAction(\''.$this->action.'\', \''.$this->controller.'\');'.
		'$_tpl_appName = $this->windSystemConfig->getAppClass();'.
		'$_tpl_app = $this->windFactory->getInstance($_tpl_appName);'.
		'$_tpl_app->getDispatcher()->setDisplay(true);'.
		'$_tpl_app->doDispatch($_tpl_forward);'.
		'list($viewName, $tplVars) = $_tpl_app->getDispatcher()->getAttribute("viewCache");'.
		'$this->windAssign($tplVars, $viewName);';
		return $_tmp;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('action', 'controller');
	}

}