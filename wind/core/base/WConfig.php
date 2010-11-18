<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
W::import('WIND:utilities.container.WModule');

/**
 * 配置信息
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WConfig extends WModule {
	
	/**
	 * 配置信息解析方法
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function parse($configSystem, $configCustom) {}

	
	/**
	 * 配置信息解析方法
	 * @param xml $configSystem
	 * @param xml $configCustom
	 */
	public function parseXML($configSystem, $configCustom) {}

	
	/**
	 * 根据配置名称获得配置信息
	 * @param string $configName
	 */
	public function getConfig($configName) {}


}