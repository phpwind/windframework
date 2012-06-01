<?php
Wind::import('WIND:viewer.IWindView');
/**
 * 命令行模式的view处理器
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package command
 */
class WindCommandView extends WindModule implements IWindView {
	
	protected $_output;
	
	public function __construct($output) {
		$this->_output = $output;
	}
	
	/* (non-PHPdoc)
	 * @see IWindView::render()
	 */
	public function render() {
		$out = '';
		foreach ($this->_output as $v) {
			if (is_object($v)) $v = get_object_vars($v);
			if (is_array($v)) 
				$out .= var_export($v, true) . "\n";
			else
			 $out .= $v . "\n";
		}
		Wind::getApp()->getResponse()->setOutput($out);
	}

}

?>