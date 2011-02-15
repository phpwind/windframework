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
			$config[WindFileCache::CACHEDIR] = dirname(dirname(dirname(dirname(__FILE__)))).'/data/cache/file/';
			$this->fileCache = new WindFileCache($config);
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
		require_once ('component/cache/stored/WindFileCache.php');
	}
	
	public function testAdd(){
		$this->assertTrue(false !== $this->fileCache->add('key','value'));
		$this->assertEquals('value',$this->fileCache->fetch('key'));
		$this->assertTrue($this->fileCache->delete('key'));
		$this->assertTrue(false !== $this->fileCache->add('newKey','newValue',1));
	}
	
	public function testSet(){
		$this->assertTrue(false !== $this->fileCache->add('key','value'));
		$this->assertEquals('value',$this->fileCache->fetch('key'));
		$this->assertTrue(false !== $this->fileCache->set('key','newValue'));
		$this->assertEquals('newValue',$this->fileCache->fetch('key'));
		$this->assertTrue($this->fileCache->delete('key'));
		$this->assertTrue(false !== $this->fileCache->set('newkey','newValue',1));
		$this->assertEquals('newValue',$this->fileCache->fetch('newkey'));
	}
	
	public function testReplace(){
		$this->assertTrue(false !== $this->fileCache->add('key','value'));
		$this->assertEquals('value',$this->fileCache->fetch('key'));
		$this->assertTrue(false !== $this->fileCache->replace('key','newValue'));
		$this->assertEquals('newValue',$this->fileCache->fetch('key'));
		$this->assertTrue($this->fileCache->delete('key'));
	}
	
	public function testFetch(){
		$this->assertTrue(false !== $this->fileCache->add('key','value',1));
		$this->assertEquals('value',$this->fileCache->fetch('key'));
	}
	
	public function testBatchFetch(){
		$this->assertTrue(false !== $this->fileCache->add('name','phpwind',1));
		$this->assertTrue(false !== $this->fileCache->add('age','100',1));
		$result = $this->fileCache->batchFetch(array('name','age'));
		$this->assertTrue(2 === count($result) && 'phpwind' === $result['name'] && '100' === $result['age']);
		$this->assertTrue($this->fileCache->batchDelete(array('name','age')));
	}
	
	public function testDelete(){
		$this->assertTrue(false !== $this->fileCache->add('name','phpwind'));
		$this->assertTrue($this->fileCache->delete('name'));
	}
	
	public function testBatchDelete(){
		$this->assertTrue(false !== $this->fileCache->add('name','phpwind',1));
		$this->assertTrue(false !== $this->fileCache->add('age','100',1));
		$this->assertTrue($this->fileCache->batchDelete(array('name','age')));
	}
	
	public function testFlush(){
		$this->assertTrue(false !== $this->fileCache->add('one','ones'));
		$this->assertTrue(false !== $this->fileCache->add('two','twos'));
		$this->assertTrue(false !== $this->fileCache->add('three','threes'));
		$this->assertTrue($this->fileCache->flush());
		$this->assertTrue($this->fileCache->add('one','ones',1));
	}
	
	public function testMutiLevels(){
		$config[WindFileCache::CACHEDIR] = dirname(dirname(dirname(dirname(__FILE__)))).'/data/cache/file/';
		$config[WindFileCache::LEVEL] = 2;
		$fileCache = new WindFileCache($config);
		$this->assertTrue(false !== $fileCache->add('school','alibaba'));
		$this->assertTrue(false !== $fileCache->add('bbs','phpwind'));
		$this->assertTrue(false !== $fileCache->set('colleage','beida'));
		$this->assertEquals('alibaba',$fileCache->fetch('school'));
		$this->assertTrue($fileCache->flush());
	}
}