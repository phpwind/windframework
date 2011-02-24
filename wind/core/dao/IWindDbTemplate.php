<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindDbTemplate {

	/**
	 * 返回数据库连接句柄
	 */
	public function getConnection();

	/**
	 * 设置db链接对象
	 * @param object $connection
	 */
	public function setConnection($connection);
}

?>