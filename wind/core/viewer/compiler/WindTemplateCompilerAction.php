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
		$_content = '<?php ';
		$_content .= '$this->doSubAction("' . $this->action . '","' . $this->controller . '");';
		return $_content . ' ?>';
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('action', 'controller');
	}

}