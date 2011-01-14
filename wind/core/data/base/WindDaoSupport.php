<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 以标准的方式使用不同的数据访问技术,方便不同数据库持久化技术间切换及各种技术中特定的异常
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindDaoSupport{
	protected $template = null;
	public function __construct(){
		$this->init();
	}
	public abstract function init();
	public abstract function setTemplate();
	public abstract function getTemplate();
}
