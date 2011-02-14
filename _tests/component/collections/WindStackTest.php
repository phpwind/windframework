<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindQueueTest extends BaseTestCase{
	/**
	 * @var WindStack
	 */
	private $stack = null;
	public function init() {
		$this->requireFile();
		if ($this->stack == null) {
			$this->stack = new WindStack();
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
		require_once ('component/collections/WindStack.php');
	}
	
	public function testPush(){
		$this->assertTrue(1 === $this->stack->push("push"));
		$this->assertEquals('push',$this->stack->peek());
	}
	
	public function testPop(){
		$this->assertTrue(1 === $this->stack->push("push"));
		$this->assertTrue(2 === $this->stack->push("pop"));
		$this->assertEquals('pop',$this->stack->pop());
		$this->assertTrue(false === $this->stack->contain('pop'));
	}
	
	public function testPeek(){
		$this->assertTrue(1 === $this->stack->push("push"));
		$this->assertEquals('push',$this->stack->peek());
		$this->assertTrue($this->stack->contain('push'));
	}
	
	

	public function testMergeFromArray() {
		$this->assertTrue(1 === $this->stack->push('one'));
		$this->assertTrue(2 === $this->stack->push('two'));
		$this->assertTrue($this->stack->mergeFromArray(array('three')));
		$this->assertTrue($this->stack->contain('three'));
	}
	
	public function testMergeFromStack() {
		$queue = new WindStack();
		$queue->push('three');
		$this->assertTrue(1 === $this->stack->push('one'));
		$this->assertTrue(2 === $this->stack->push('two'));
		$this->assertTrue($this->stack->mergeFromStack($queue));
		$this->assertTrue($this->stack->contain('three'));
	}
	
	public function testClear() {
		$this->assertTrue(1 === $this->stack->push('one'));
		$this->assertTrue(2 === $this->stack->push('two'));
		$this->assertTrue($this->stack->clear());
		$this->assertTrue(0 === $this->stack->getCount());
		
	}
	
	public function testClone(){
		$stack = clone($this->stack);
		$this->assertTrue(($stack instanceof WindStack) && $stack !== $this->stack);
	}
	
	public function testCount(){
		$this->assertTrue(1 === $this->stack->push('one'));
		$this->assertEquals(1,count($this->stack));
	}
}