<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindMysqlPdoAdapter extends PDO {

	public function setCharset($charset) {
		if (!$charset) $charset = 'gbk';
		$this->query("set names " . $this->quote($charset) . ";");
	}
}
?>