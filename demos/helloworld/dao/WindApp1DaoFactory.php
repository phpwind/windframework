<?php

L::import('WIND:core.dao.AbstractWindDaoFactory');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindApp1DaoFactory extends AbstractWindDaoFactory {

	protected static $instance = null;

	/* (non-PHPdoc)
	 * @see AbstractWindDaoFactory::getFactory()
	 */
	public function getFactory() {
		if (self::$instance === null) {
			self::$instance = new WindApp1DaoFactory();
		}
		return self::$instance;
	}
}

?>