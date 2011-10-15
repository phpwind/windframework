<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
Wind::import('WIND:utility.WindSecurity');
/**
 * 变量输出编译类
 * 
 * 变量输出格式:<code>
 * 变量名称|变量格式（html，text）
 * {$var|html}   //不执行编译
 * {@$var->a|text}  //执行编译
 * {@templateName:var|html}</code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 * @subpackage compiler
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
			$_args = explode('.', $_var . '.');
			$_output = 'Wind::getApp()->getResponse()->getData(\'' . $_namespace . '\'';
			foreach ($_args as $_arg) {
				$_arg && $_output .= ',\'' . $_arg . '\'';
			}
			$_output .= ')';
		}
		if (!strcasecmp($type, 'html'))
			return '<?php echo ' . $_output . ';?>';
		else
			return '<?php echo WindSecurity::escapeHTML(' . $_output . ');?>';
	}
}
?>