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
class WindQueueTest extends BaseTestCase{
	/**
	 * @var WindQueue
	 */
	private $queue = null;
	public function init() {
		$this->requireFile();
		if ($this->queue == null) {
			$this->queue = new WindQueue();
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
		require_once ('component/collections/WindQueue.php');
	}
	
	public function testEnqueue(){
		$this->assertTrue(1 === $this->queue->enqueue("enqueue"));
		$this->assertEquals('enqueue',$this->queue->peek());
	}
	
	public function testDequeue(){
		$this->assertTrue(1 === $this->queue->enqueue("enqueue"));
		$this->assertTrue(2 === $this->queue->enqueue("dequeue"));
		$this->assertEquals('enqueue',$this->queue->dequeue());
		$this->assertTrue(false === $this->queue->contain('enqueue'));
	}
	
	public function testPeek(){
		$this->assertTrue(1 === $this->queue->enqueue("enqueue"));
		$this->assertEquals('enqueue',$this->queue->peek());
		$this->assertTrue($this->queue->contain('enqueue'));
	}
	
	

	public function testMergeFromArray() {
		$this->assertTrue(1 === $this->queue->enqueue('one'));
		$this->assertTrue(2 === $this->queue->enqueue('two'));
		$this->assertTrue($this->queue->mergeFromArray(array('three')));
		$this->assertTrue($this->queue->contain('three'));
	}
	
	public function testMergeFromQueue() {
		$queue = new WindQueue();
		$queue->enqueue('three');
		$this->assertTrue(1 === $this->queue->enqueue('one'));
		$this->assertTrue(2 === $this->queue->enqueue('two'));
		$this->assertTrue($this->queue->mergeFromQueue($queue));
		$this->assertTrue($this->queue->contain('three'));
	}
	
	public function testClear() {
		$this->assertTrue(1 === $this->queue->enqueue('one'));
		$this->assertTrue(2 === $this->queue->enqueue('two'));
		$this->assertTrue($this->queue->clear());
		$this->assertTrue(0 === $this->queue->getCount());
		
	}
	
	public function testClone(){
		$queue = clone($this->queue);
		$this->assertTrue(($queue instanceof WindQueue) && $queue !== $this->queue);
	}
	
	public function testCount(){
		$this->assertTrue(1 === $this->queue->enqueue('one'));
		$this->assertEquals(1,count($this->queue));
	}
}