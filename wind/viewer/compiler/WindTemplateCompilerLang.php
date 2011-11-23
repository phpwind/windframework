<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * 输出翻译后的语言信息
 * 
 * <code>
 * <lang message = '' />
 * </code>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 * @subpackage compiler
 */
class WindTemplateCompilerLang extends AbstractWindTemplateCompiler {
	
	protected $message = '';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if (!$this->message) return $content;
		$resource = Wind::getApp()->getComponent('i18n');
		$resource !== null && $this->message = $resource->getMessage($this->message);
		$_content = '<?php echo "' . $this->message . '" ?>';
		return $_content;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	protected function getProperties() {
		return array('message');
	}
}
