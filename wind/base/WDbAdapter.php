<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */


/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WDbAdapter{
	protected $linked = array();
	protected $linking = null;
	protected $queryId = '';
	protected $last_sql = '';
	protected $last_errstr = '';
	protected $last_errcode = 0;
	protected $sqlBuilder = null;
	protected $is_conntected = 0;
	
	
	protected $transCounter = 0;
	public $enableSavePoint = 0;
	protected $savepoint = array();
	protected function __construct(){
		
	}
	
	private function parseConfig(){
		
	}
	private function parseDSN();
	protected function connect();
	public function addConnect();
	public function switchConnect();
	public function switchDataBase();
	public function query();
	public function exceute();
	public function insert();
	public function update();
	public function select();
	public function delete();
	public function getAll();
	
	protected function close();
	protected function dispose();
	public function savePoint();
	public function beginTrans();
	public function rollbackTrans();
	public function getAffectedRows();
	public function getInsertId();
	public function getLastSql(){
		return $this->sql;
	}
	
	

}