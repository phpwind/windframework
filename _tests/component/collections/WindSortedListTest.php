<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindListTest extends BaseTestCase {
	/**
	 * @var WindSortedList
	 */
	private $sortedList = null;
	public function init() {
		$this->requireFile();
		if ($this->sortedList == null) {
			$this->sortedList = new WindSortedList();
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function requireFile(){
		require_once ('component/collections/WindSortedList.php');
	}
	
	public function testAdd() {
		$this->assertTrue($this->sortedList->add('key','value'));
		$this->assertEquals('value', $this->sortedList->item('key'));
	}
	
	public function testContains(){
		$this->assertTrue($this->sortedList->add('key','value'));
		$this->assertTrue($this->sortedList->contains('key'));
		$this->assertTrue($this->sortedList->containsIndex(0));
		$this->assertTrue($this->sortedList->containsValue('value'));
	}
	
	public function testItem() {
		$this->assertTrue($this->sortedList->add('a','11'));
		$this->assertTrue($this->sortedList->add('b','22'));
		$this->assertTrue($this->sortedList->add('c','33'));
		$this->assertEquals('33', $this->sortedList->itemAt(2));
		$this->assertEquals('33', $this->sortedList->item('c'));
	}
	
	public function testIndexOf() {
		$this->assertTrue($this->sortedList->add('a','11'));
		$this->assertTrue($this->sortedList->add('b','22'));
		$this->assertTrue($this->sortedList->add('c','33'));
		$this->assertTrue(2 == $this->sortedList->indexOfKey('c'));
		$this->assertTrue(2 == $this->sortedList->indexOfValue('33'));
	}
	
	public function testSetByIndex() {
		$this->assertTrue($this->sortedList->add('a','one'));
		$this->assertTrue($this->sortedList->add('b','two'));
		$this->assertTrue($this->sortedList->setByIndex(1, 'test'));
		$this->assertEquals('test', $this->sortedList->itemAt(1));
		$this->assertTrue(false === $this->sortedList->containsValue('two'));
		$this->assertTrue($this->sortedList->containsKey('b'));
	}
	
	public function testSetByKey(){
		$this->assertTrue($this->sortedList->add('a','one'));
		$this->assertTrue($this->sortedList->add('b','two'));
		$this->assertEquals('two',$this->sortedList->setByKey('b', 'test'));
		$this->assertEquals('test', $this->sortedList->item('b'));
		$this->assertTrue(false === $this->sortedList->containsValue('two'));
		$this->assertTrue($this->sortedList->containsKey('b'));
	}
	
	public function testRemove() {
		$this->assertTrue($this->sortedList->add('a','one'));
		$this->assertTrue($this->sortedList->add('b','two'));
		$this->assertEquals('two', $this->sortedList->remove('b'));
		$this->assertTrue(false === $this->sortedList->containsValue('two'));
		$this->assertTrue(false === $this->sortedList->containsKey('b'));
	}
	
	public function testRemoveAt() {
		$this->assertTrue($this->sortedList->add('a','one'));
		$this->assertTrue($this->sortedList->add('b','two'));
		$this->assertEquals('two', $this->sortedList->removeAt(1));
		$this->assertTrue(false === $this->sortedList->containsValue('two'));
		$this->assertTrue(false === $this->sortedList->containsKey('b'));
	}
	
	public function testClone(){
		$sortedList = clone($this->sortedList);
		$this->assertTrue(($sortedList instanceof WindSortedList) && $sortedList !== $this->sortedList);
	}
	
	
	
	public function testClear() {
		$this->assertTrue($this->sortedList->add('a','one'));
		$this->assertTrue($this->sortedList->add('b','two'));
		$this->assertTrue($this->sortedList->clear());
		$this->assertTrue(0 === $this->sortedList->getCount());
	}
	
	public function testFixedList(){
		$list = new WindSortedList(array('key','value'));
		try{
			$list->add('key',"haha");
		}catch(WindException $e){
			$this->assertTrue($list->getIsFixedSize());
			return true;
		}
		$this->fail("this sortedlist is fixed");
	}
	
	public function testReadOnly(){
		$list = new WindSortedList(array('key','value'),true);
		try{
			$list->setByIndex(0,"haha");
			$list->setByKey('key',"haha");
		}catch(WindException $e){
			$this->assertTrue($list->getIsReadOnly());
			$this->assertTrue($list->getIsFixedSize());
			return true;
		}
		$this->fail("this list is readonly");
	}
	
	public function testIndexVisit(){
		$this->sortedList['version'] = 'phpwind 8.0';
		$this->sortedList['key'] = 'value';
		$this->assertEquals('phpwind 8.0',$this->sortedList['version']);
		$this->assertEquals('phpwind 8.0',$this->sortedList[0]);
		$this->assertTrue(2 === count($this->sortedList));
		if(isset($this->sortedList[1])){
			unset($this->sortedList[1]);
		}
		$this->assertTrue(1 === count($this->sortedList));
	}
}