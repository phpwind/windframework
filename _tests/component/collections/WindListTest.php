<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 列表集合单元测试
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindListTest extends BaseTestCase {
	/**
	 * @var WindList
	 */
	private $list = null;
	public function init() {
		$this->requireFile();
		if ($this->list == null) {
			$this->list = new WindList();
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
		require_once ('component/collections/WindList.php');
	}
	
	public function testAdd() {
		$this->assertTrue($this->list->add('test'));
		$this->assertEquals('test', $this->list->itemAt(0));
	}
	
	public function testInsertAt() {
		$this->assertTrue($this->list->insertAt(0, 'test'));
		$this->assertEquals('test', $this->list->itemAt(0));
	}
	
	public function testItemAt() {
		$this->assertTrue($this->list->add('a'));
		$this->assertTrue($this->list->add('b'));
		$this->assertTrue($this->list->add('c'));
		$this->assertEquals('c', $this->list->itemAt(2));
	
	}
	
	public function testIndexOf() {
		$this->assertTrue($this->list->add('a'));
		$this->assertTrue($this->list->add('b'));
		$this->assertTrue($this->list->add('c'));
		$this->assertTrue(2 == $this->list->indexOf('c'));
	}
	
	public function testContain() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->add('three'));
		$this->assertTrue($this->list->contain(('two')));
	}
	
	public function testContainAt() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->add('three'));
		$this->assertTrue($this->list->containAt(2));
	}
	
	public function testModify() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->add('three'));
		$this->assertTrue($this->list->modify(1, 'test'));
		$this->assertEquals('test', $this->list->itemAt(1));
		$this->assertTrue(false === $this->list->contain('two'));
	}
	
	public function testRemove() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertEquals('two', $this->list->remove('two'));
		$this->assertTrue(false === $this->list->contain('two'));
	}
	
	public function testRemoveAt() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertEquals('two', $this->list->removeAt(1));
		$this->assertTrue(false === $this->list->contain('two'));
	}
	
	public function testMergeFromArray() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->mergeFromArray(array('three')));
		$this->assertTrue($this->list->contain('three'));
	}
	public function testMergeFromList() {
		$list = new WindList();
		$list->add('three');
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->mergeFromList($list));
		$this->assertTrue($this->list->contain('three'));
	}
	
	public function testClear() {
		$this->assertTrue($this->list->add('one'));
		$this->assertTrue($this->list->add('two'));
		$this->assertTrue($this->list->clear());
		$this->assertTrue(0 === $this->list->getCount());
		$list = $this->list->getList();
		$this->assertTrue(empty($list));
	}
	
	public function testFixedList(){
		$list = new WindList(array('abc'));
		try{
			$list->add("haha");
		}catch(WindException $e){
			$this->assertTrue($list->getIsFixedSize());
			return true;
		}
		$this->fail("this list is fixed");
	}
	
	public function testReadOnly(){
		$list = new WindList(array("read"),true);
		try{
			$list->modify(0,"haha");
		}catch(WindException $e){
			$this->assertTrue($list->getIsReadOnly());
			$this->assertTrue($list->getIsFixedSize());
			return true;
		}
		$this->fail("this list is readonly");
	}
	
	public function testIndexVisit(){
		$this->list[0] = 'phpwind';
		$this->list[1] = 'test';
		$this->assertEquals('phpwind',$this->list[0]);
		$this->assertTrue(2 === count($this->list));
		if(isset($this->list[1])){
			unset($this->list[1]);
		}
		$this->assertTrue(1 === count($this->list));
	}
}