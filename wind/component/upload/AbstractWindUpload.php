<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import ( 'WIND:component.utility.Security' );
L::import ( 'WIND:component.utility.WindFile' );
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
	public function upload($name, $newName, $path);
}