<?php

L::import('WIND.core.dao.AbstractWindDao');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindApp1UserDao extends AbstractWindDao {

	public function findUserById($userId) {
		
	}	

	public function getCacheMethods() {
		return array('findUserById');
	}
}

?>