<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import ( 'WIND:component.db.WindConnectionManager' );
class DbManagerController extends WindController{
	private $manager;
	public function getManager(){
		$this->manager = new  WindConnectionManager();
	}
	public function run(){
		echo 'This is distribute demo,ha ha !';
	
	}
	public function conn(){
		$this->getManager();
		$conn = $this->manager->getconnection('','');
		echo '<pre>';
		echo 'This is Random Connection';
		print_r($conn);
	
	}
	
	public function master(){
		$this->getManager();
		$conn = $this->manager->getMasterConnection();
		echo '<pre>';
		echo 'This is Master Connection';
		print_r($conn);
	}
	
	public function slave(){
		$this->getManager();
		$conn = $this->manager->getSlaveConnection();
		echo '<pre>';
		echo 'This is Slave Connection';
		print_r($conn);
	}
	
}