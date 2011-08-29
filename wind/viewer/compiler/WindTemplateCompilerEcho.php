<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
Wind::import('COM:utility.WindSecurity');
/**
 * 变量输出解析 
 * 变量名称|变量格式（html，text）
 * {$var|html}   //不执行编译
 * {@$var->a|text}  //执行编译
 * {@templateName:var|html}   
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerEcho extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$_output = preg_replace(array('/^[\n\s{\@]+/i', '/[\n\s}\;]+$/i'), array('', ''), $content);
		list($_output, $type) = explode('|', $_output . '|');
		if (strpos($_output, '::') === false && strpos($_output, ':') !== false) {
			list($_namespace, $_var) = explode(':', $_output);
			$_output = 'Wind::getApp()->getResponse()->getData(\'' . $_namespace . '\', \'' . $_var . '\')';
		}
		if (!strcasecmp($type, 'html'))
			return '<?php echo ' . $_output . ';?>';
		else
			return '<?php echo WindSecurity::escapeHTML(' . $_output . ');?>';
	}
}
?>