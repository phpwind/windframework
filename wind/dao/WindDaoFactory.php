<?php
Wind::import('WIND:dao.exception.WindDaoException');
/**
 * Dao工厂
 * 
 * 职责：
 * 创建DAO实例
 * 数据缓存部署实现
 * 创建数据访问连接对象
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDaoFactory extends WindModule {
	/**
	 * dao路径信息
	 * @var string
	 */
	protected $daoResource = '';

	/**
	 * 返回Dao类实例
	 * $className接受两种形式呃参数如下
	 * 'namespace:path'
	 * 'className'
	 * 
	 * @param string $className
	 * @return WindDao
	 */
	public function getDao($className) {
		try {
			if (strpos($className, ":") === false)
				$className = $this->getDaoResource() . '.' . $className;
			Wind::getApp()->getWindFactory()->addClassDefinitions($className, 
				array('path' => $className, 'scope' => 'application'));
			$daoInstance = Wind::getApp()->getWindFactory()->getInstance($className);
			$daoInstance->setDelayAttributes(array('connection' => array('ref' => 'db')));
			return $daoInstance;
		} catch (Exception $exception) {
			throw new WindDaoException(
				'[dao.WindDaoFactory] create dao ' . $className . ' fail.' . $exception->getMessage());
		}
	}

	/**
	 * 获得dao存放的目录
	 * @return string $daoResource
	 */
	public function getDaoResource() {
		return $this->daoResource;
	}

	/**
	 * 设置dao的获取目录
	 * @param string $daoResource
	 */
	public function setDaoResource($daoResource) {
		$this->daoResource = $daoResource;
	}
}
?>