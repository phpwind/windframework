<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import('WIND:component.message.WindMessage');

/**
 * WindMessage
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindMessageTest extends BaseTestCase {
	private $message = null;
	public function setUp() {
		$this->message = new WindMessage();
	}
	public function tearDown() {
		$this->message = null;
	}
	public static function providerWithGetMess() {
		return array();
	}
	private function addTestMessage() {
		$_tmp = array('one' => 'php', 'two' => $this->message, 'three' => '', 'four', 
			'five' => array('six' => 'true', 'seven' => 'false'));
		$this->message->addMessage($_tmp);
	}
	/**
	 * 测试获得信息：
	 * 1：当信息数组中没有值的时候获取一个信息
	 */
	public function testGetMessageWithNone() {
		$this->assertEquals('', $this->message->getMessage('name'));
	}
	
	/**
	 * 2：当信息数组中没有值的时候获取整个信息
	 */
	public function testGetMessageWithNull() {
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 0);
		$this->addTestMessage();
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 5);
	}
	
	/**
	 * 3：设置一个信息，并且能正确获得该信息
	 */
	public function testGetMessageWithName() {
		$this->addTestMessage();
		$this->assertEquals('php', $this->message->getMessage('one'));
		$this->assertEquals('four', $this->message->getMessage(0));
		$this->assertEquals('false', $this->message->getMessage('seven'));
		$this->assertEquals('true', $this->message->getMessage('six'));
	}
	/**
	 * 4：设置一个对象，并能正确获得该对象
	 */
	public function testGetMessageWithObjectName() {
		$this->addTestMessage();
		$obj = $this->message->getMessage('two');
		$this->assertTrue(is_object($obj) && $obj instanceof WindMessage);
		$this->message->addMessage($this, 'case');
		$obj = $this->message->getMessage('case');
		$this->assertTrue(is_object($obj) && $obj instanceof PHPUnit_Framework_TestCase);
	}
	
	/**
	 * 添加一条信息：
	 * 1：添加一条空信息
	 */
	public function testAddMessageWithEmpty() {
		$this->message->addMessage('', 'php');
		$this->assertEquals('', $this->message->getMessage('php'));
	}
	/**
	 * 2：添加一条字串信息
	 */
	public function testAddMessageWithNull() {
		$this->message->addMessage(null, 'php');
		$this->assertEquals('', $this->message->getMessage('php'));
	}
	/**
	 * 3：添加对象
	 */
	public function testAddMessageWithObject() {
		$this->message->addMessage($this, 'obj');
		$obj = $this->message->getMessage('obj');
		$this->assertTrue(is_object($obj) && $obj instanceof PHPUnit_Framework_TestCase);
	}
	/**
	 * 4: 添加字串
	 */
	public function testAddMessageWithString() {
		$this->message->addMessage('hello world', 'php');
		$this->assertEquals('hello world', $this->message->getMessage('php'));
		$this->message->addMessage('phpWind', 'test');
		$this->assertEquals('phpWind', $this->message->getMessage('test'));
	}
	
	/**
	 * 5: 添加已存在的字串
	 */
	public function testAddMessageWithSameName() {
		$this->message->addMessage('hello world', 'php');
		$this->assertEquals('hello world', $this->message->getMessage('php'));
		$this->message->addMessage('phpWind', 'php');
		$this->assertEquals('phpWind', $this->message->getMessage('php'));
	}
	
	/**
	 * 测试清空信息
	 * 1：清空单个信息
	 */
	public function testClearWithName() {
		$this->addTestMessage();
		$this->assertEquals('php', $this->message->getMessage('one'));
		$this->message->clear('one');
		$this->assertEquals('', $this->message->getMessage('one'));
		$this->assertEquals('four', $this->message->getMessage(0));
		$this->message->clear(0);
		$this->assertEquals('', $this->message->getMessage(0));
	}
	/**
	 * 2：清空所有
	 */
	public function testClearWithAll() {
		$this->addTestMessage();
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 5);
		$this->message->clear();
		$this->assertEquals('', $this->message->getMessage('one'));
		$this->assertEquals('', $this->message->getMessage(0));
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 0);
	}
	/**
	 * 3：清空输入空字串
	 */
	public function testClearWithEmpty() {
		$this->message->addMessage('phpUnit', 'test');
		$this->assertEquals('phpUnit', $this->message->getMessage('test'));
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 1);
		$this->message->clear('');
		$this->assertEquals('', $this->message->getMessage('test'));
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 0);
	}
	
	/**
	 * 4：清空输入null
	 */
	public function testClearWithNull() {
		$this->message->addMessage('phpUnit', 'test');
		$this->assertEquals('phpUnit', $this->message->getMessage('test'));
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 1);
		$this->message->clear(null);
		$this->assertEquals('', $this->message->getMessage('test'));
		$_tmp = $this->message->getMessage();
		$this->assertTrue(is_array($_tmp) && count($_tmp) == 0);
	}
}