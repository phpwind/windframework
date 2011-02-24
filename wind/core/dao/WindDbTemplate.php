<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDbTemplate implements IWindDbTemplate {

	private $connection = null;

	/**
	 * @return the $connection
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * @param object $connection
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}
}

?>