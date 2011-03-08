<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class testTest extends PHPUnit_Framework_TestCase {
	
	public function testEmpty() {
		$stack = array();
		$this->assertTrue(empty($stack));
		return $stack;
	}
	/**
	 * @depends testEmpty 
	 */
	public function testPush(array $stack) {
		array_push($stack, 'foo');
		$this->assertEquals('foo', $stack[count($stack) - 1]);
		$this->assertTrue(false === empty($stack));
		return $stack;
	}
	/**
	 * @depends testPush 
	 */
	public function testPop(array $stack) {
		print_r($stack);
		echo 2;
		$this->assertEquals('foo', array_pop($stack));
		$this->assertTrue(empty($stack));
	}
	
	/**
	 * @dataProvider provider
	 * @group a
	 */
	public function testAdd($a, $b, $c) {
		$this->assertEquals($c, $a + $b);
	}
	
	public static function provider() {
		return array(array(0, 0, 0), array(0, 1, 1), array(1, 0, 1), array(1, 2, 3));
	}
	
	/**
	 * @test
	 */
	public function Exception() {
		$this->setExpectedException('exception');
		throw new Exception("ha");
	}
	
	public function testHa() {
		$this->assertTrue(true);
	}
	/**
	 * @covers Calculator::add
	 */
	public function testAdds() {
		$o = new Calculator();
		$o->test();
		$this->assertEquals(0, $o->add(0, 0));
	}
	
	public function testIncomplete() {
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
	
	public function testSkip() {
		$this->markTestSkipped('This test has not been implemented yet.');
	}
	
	public function testStub() {
		$stub = $this->getMock('SomeClass', array('doSomething'));
		$stub->expects($this->any())->method('doSomething')->will($this->returnValue('foo'));
		echo $stub->doSomething();
		// 调用$stub->doSomething()会立刻返回“foo”。
	}
	
	public function testNotEquals() {
		$constraint = $this->equalTo('foo');
		$this->assertThat('foo', $constraint);
	}

}

class Calculator {
	
	/**
	 * @assert (0, 0) == 0     
	 * @assert (0, 1) == 1 
	 * @assert (1, 0) == 1     
	 * @assert (1, 1) == 2     
	 */
	public function add($a, $b) {
		if ($a == 1000) {
			//@codeCoverageIgnoreStart
			throw new Exception("");
			throw new Exception("");
			throw new Exception("");
			//@codeCoverageIgnoreEnd
		}
		return $a + $b;
	}
	
	public function test() {
		echo 33;
	}
}