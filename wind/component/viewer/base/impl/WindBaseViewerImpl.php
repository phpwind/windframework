<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 视图引擎基类
 * 通过继承该方法可以实现对视图模板的调用解析
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface WindBaseViewerImpl {
	
	public function setTpl($tpl = '');
	
	public function setLayout($layout = null);
	
	public function windDisplay($tpl = '');
	
	public function windAssign($vars = '', $key = null);
	
	public function windFetch();
	
	public function setCacheDir($cacheDir); //设置缓存路径
	
	public function setCompileDir($compileDir);//设置编译路径
	
	public function setTemplateDir($templateDir);//设置模板路径
}