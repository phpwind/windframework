<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import ( 'WIND:component.utility.Security' );
Wind::import ( 'WIND:component.utility.WindFile' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindUpload{
	/**
	 * @param string $name
	 * @param string $newName
	 * @param string $path
	 */
	public abstract function upload($name, $newName, $path);
}