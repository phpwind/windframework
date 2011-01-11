<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindDebugTest extends BaseTestCase {
	
	public function init() {
		require_once ('component/Log/WindDebug.php');
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetMemUsage(){
		$this->assertTrue(is_float(WindDebug::getMemUsage()));
	}
	
	public function testGetExecTime(){
		$this->assertTrue(is_float(WindDebug::getExecTime()));
	}
	
	public function testGetMemUsageOfp2p(){
		WindDebug::setBreakPoint('start');
		for($i=0;$i<10;$i++){
			$i++;
		}
		WindDebug::setBreakPoint('end');
		$mem = WindDebug::getMemUsageOfp2p('start','end');
		$this->assertTrue(is_float($mem));
	}
	
	public function testGetExecTimeOfp2p(){
		WindDebug::setBreakPoint('start');
		for($i=0;$i<10;$i++){
			$i++;
		}
		WindDebug::setBreakPoint('end');
		$time = WindDebug::getExecTimeOfp2p('start','end');
		$this->assertTrue(is_float($time));
	}
	
	public function testTrace(){
		$this->assertTrue(is_array(WindDebug::trace()));
	}
	
	public function testDebug(){
		$this->assertTrue(is_string(WindDebug::debug("suqian")));
	}
	
	public function testDebugWithBreakPoint(){
		WindDebug::setBreakPoint('start');
		for($i=0;$i<10;$i++){
			$i++;
		}
		WindDebug::setBreakPoint('end');
		$this->assertTrue(is_string(WindDebug::debug("suqian",array(),'start','end')));
	}
	
	public function testRemoveBreakPoint(){
		WindDebug::setBreakPoint('break');
		$break = WindDebug::getBreakPoint('break');
		$this->assertTrue(is_array($break));
		WindDebug::removeBreakPoint('break');
		$break = WindDebug::getBreakPoint('break');
		$this->assertTrue(empty($break));
		
	}
	
	public function testGetIncludeFiles(){
		$includeFiles = WindDebug::loadFiles();
		$this->assertTrue(is_array($includeFiles));
	}
}