<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-26
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 配置文件解析类的接口
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
interface IWindParser {
	public function loadFile($fileName);
	public function loadXMLString($string);
	public function parser(); 
	public function getResult();
	public function getGlobalTags();
	public function getMergeTags();
}