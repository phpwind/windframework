<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindDbTemplate extends WindTemplate{
	/**
	 * @var WindConnectionManager 分布式管理与数据库驱动工厂
	 */
	protected $distributed = null;
	/**
	 * @var WindSqlBuilder sql语句组装器
	 */
	protected $sqlBuilder = null;
	/**
	 * @var WindDbAdapter 数据库操作适配器
	 */
	protected $connection = null;
	
	/**
	 * @var boolean 是否是分布式
	 */
	protected $ifMutiDb = false;
	
	public function __construct(array $config){
		$className = L::import ( 'WIND:component.db.WindConnectionManager' );
		$this->distributed = new WindConnectionManager($config);
		$this->ifMutiDb = 1 < count($config[IWindDbConfig::CONNECTIONS]);
		
	}
	
	public function setConnAndBuilder($type=IWindDbConfig::CONN_MASTER){
		if($this->ifMutiDb){
			$this->connection = $this->distributed->getConnection('',$type);
		}else{
			$this->connection = $this->distributed->getConnection('','');
		}
		$this->sqlBuilder = $this->connection->getSqlBuilder();
	}
	
	public function queryForObject(){
		
	}
	
	public function queryForArray(){
		
	}
	
	
	
	public function insert(){
		
	}
	
	public function delete(){
		
	}
	
	public function update(){
		
	}
	
	public function query(){
		
	}
	
	public function write($sql){
		$this->setConnAndBuilder(IWindDbConfig::CONN_MASTER);
		return $this->connection->query($sql);
	}
	
	public function read($sql){
		$this->setConnAndBuilder(IWindDbConfig::CONN_SLAVE);
		return $this->connection->query($sql);
	}

}