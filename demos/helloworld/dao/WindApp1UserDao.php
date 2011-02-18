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

		//print_r($this->queryBySql("select * from pw_members"));
		print_r($this->getMasterConnection()->getSqlBuilder());
		
	

		
	}	


	public function getCacheMethods() {
		return array('findUserById');
	}
}

?>