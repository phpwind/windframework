<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

Wind::import('WIND:core.dao.dbtemplate.WindSimpleDbTemplate');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnectionManagerBasedDbTemplate extends WindSimpleDbTemplate {

	/**
	 * @var WindConnectionManager
	 */
	private $connectionManager = null;

	/**
	 * 设置数据库链接管理
	 * 
	 * (non-PHPdoc)
	 * @see IWindDbTemplate::setConnection()
	 * @param WindConnectionManager $connection
	 */
	public function setConnection($connectionManager) {
		$this->connectionManager = $connectionManager;
	}

	/**
	 * 获得数据库链接管理
	 * 
	 * (non-PHPdoc)
	 * @see IWindDbTemplate::getConnection()
	 * @return WindConnectionManager $connection
	 */
	public function getConnection() {
		return $this->connectionManager;
	}
    
	/**
	 * 获得数据库链接操作句柄
	 * 
	 * @return WindDbAdapter $dbHandler
	 */
	protected function getDbHandler() {
		return $this->connectionManager->getConnection();
	}
}

?>