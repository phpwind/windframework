<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindFileCacheTest extends BaseTestCase{
	/**
	 * @var WindFileCache
	 */
	private $fileCache = null;
	public function init() {
		$this->requireFile();
		if ($this->fileCache == null) {
			$config[WindFileCache::CACHEDIR] = dirname(dirname(dirname(dirname(__FILE__)))).'/data/cache/';
			$this->fileCache = new WindFileCache();
			$this->fileCache->setConfig($config);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function requireFile() {
		require_once ('component/cache/strategy/WindFileCache.php');
	}

	
	public function testSet(){
		$this->assertTrue(false !== $this->fileCache->set('key','value'));
		$this->assertEquals('value',$this->fileCache->get('key'));
		$this->assertTrue(false !== $this->fileCache->set('key','newValue'));
		$this->assertTrue(false !== $this->fileCache->delete('key'));
		$this->assertTrue(false !== $this->fileCache->set('newkey','newValue',1));
		$this->assertEquals('newValue',$this->fileCache->get('newkey'));
	}
	
	
	public function testget(){
		$this->assertTrue(false !== $this->fileCache->set('php','value',1));
		$this->assertEquals('value',$this->fileCache->get('php'));
	}
	
	public function testBatchget(){
		$this->assertTrue(false !== $this->fileCache->set('name','phpwind',1));
		$this->assertTrue(false !== $this->fileCache->set('age','100',1));
		$result = $this->fileCache->batchGet(array('name','age'));
		$this->assertTrue(2 === count($result) && 'phpwind' === $result['name'] && '100' === $result['age']);
		$this->assertTrue($this->fileCache->batchDelete(array('name','age')));
	}
	
	public function testDelete(){
		$this->assertTrue(false !== $this->fileCache->set('name','phpwind'));
		$this->assertTrue($this->fileCache->delete('name'));
	}
	
	public function testBatchDelete(){
		$this->assertTrue(false !== $this->fileCache->set('name','phpwind',1));
		$this->assertTrue(false !== $this->fileCache->set('age','100',1));
		$this->assertTrue($this->fileCache->batchDelete(array('name','age')));
	}
	
	public function testFlush(){
		$this->assertTrue(false !== $this->fileCache->set('one','ones'));
		$this->assertTrue(false !== $this->fileCache->set('two','twos'));
		$this->assertTrue(false !== $this->fileCache->set('three','threes'));
		//$this->assertTrue($this->fileCache->flush());
		$this->assertTrue($this->fileCache->set('one','ones',1));
	}
	
	public function testMutiLevels(){
		$config[WindFileCache::CACHEDIR] = dirname(dirname(dirname(dirname(__FILE__)))).'/data/cache';
		$config[WindFileCache::LEVEL] = 2;
		$fileCache = new WindFileCache($config);
		$fileCache->setConfig($config);
		$this->assertTrue(false !== $fileCache->set('school','alibaba',100));
		$this->assertTrue(false !== $fileCache->set('bbs','phpwind'));
		$this->assertTrue(false !== $fileCache->set('colleage','beida'));
		$this->assertEquals('alibaba',$fileCache->get('school'));
		//$fileCache->flush();
		
	}
}