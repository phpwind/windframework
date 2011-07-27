<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindConfigParser {

	/**
	 * 解析文件，保存缓存，返回解析结果
	 * 
	 * 1、缺省的配置文件，采用XML格式解析返回
	 * 2、如果输入的配置文件格式没有提供支持，则抛出异常
	 * 3、根据格式进行解析
	 * 4、参数三$isApp 用来配置该解析式组件格式的解析还是应用配置的解析，
	 * 如果是应用解析需要进行merge操作，如果是组件解析则不用
	 * 
	 * @param string $name       解析后保存的文件名字
	 * @param string $configPath 待解析文件的绝对路径
	 * @param string $append 追加的文件
	 * @param AbstractWindCache $cache     缓存策略
	 * @return array             解析成功返回的数据
	 */
	public function parse($configPath, $alias = '', $append = '',  AbstractWindCache $cache = null);

}