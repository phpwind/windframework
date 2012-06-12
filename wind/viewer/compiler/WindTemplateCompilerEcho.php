<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
Wind::import('WIND:utility.WindSecurity');
Wind::import('WIND:utility.WindJson');
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
		preg_match('/^[\w\s]*:[\w\s\._]*$/i', $_output, $matchs);
		if ($matchs) {
			list($_namespace, $_var) = explode(':', $_output);
			$_args = explode('.', $_var . '.');
			$_output = 'Wind::getApp()->getResponse()->getData(\'' . $_namespace . '\'';
			foreach ($_args as $_arg) {
				$_arg && $_output .= ',\'' . $_arg . '\'';
			}
			$_output .= ')';
		}
		
		switch (strtolower($type)) {
			case 'json':
				$content = '<?php echo WindJson::encode(' . $_output . ');?>';
				break;
			case 'url':
			case 'html':
			case 'js':
				$content = '<?php echo ' . $_output . ';?>';
				break;
			case 'text':
				$content = '<?php strip_tags(' . $_output . ');?>';
				break;
			default:
				$content = '<?php echo htmlspecialchars(' . $_output . ', ENT_QUOTES);?>';
		}
		return $content;
	}
}
?>