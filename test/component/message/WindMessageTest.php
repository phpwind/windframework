<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
require_once(WIND_PATH . '/component/message/WindMessage.php');

class WindMessageTest extends BaseTestCase {
	private $message = null;
	public function setUp() {
		$this->message = new WindMessage();
	}
	public function tearDown() {
		$this->message = null;
	}
	/**
	 * 测试获得信息：
	 * 1：当信息数组中没有值的时候获取一个信息
	 * 2：当信息数组中没有值的时候获取整个信息
	 * 3：设置一个信息，并且能正确获得该信息
	 * 4：设置一个对象，并能正确获得该对象
	 * 5：设置一个数组，并能正确获得该数组中的数据信息
	 * 5：当信息数组中有信息的时候获得一个信息
	 * 6：当信息数组中有信息的时候获得整个信息
	 * 7：当信息数组中有信息的时候传入一个空串获得整个信息
	 */
	public function testGetMessage() {
		$_tmp = array('one' => 'php', 'two' => $this->message, 'three' => '', 'four', 
						'five' => array('six' => 'true', 'seven' => 'false'));
		$this->message->addMessage($_tmp);
		$this->assertEquals('', $this->message->getMessage('name'));
		$this->assertTrue(is_array($this->message->getMessage()));
		
		$this->assertEquals('php', $this->message->getMessage('one'));
		
		$this->message->addMessage('wind', 'name');
		$this->assertEquals('wind', $this->message->getMessage('name'));
		
		$this->message->addMessage($this, 'me');
		$message = $this->message->getMessage('me');
		$this->assertTrue(is_object($message) && $message instanceof PHPUnit_Framework_TestCase);
		
		$message = $this->message->getMessage();
		$this->assertTrue(is_array($message) && count($message) == 7);
		
		$message = $this->message->getMessage('');
		$this->assertTrue(is_array($message) && count($message) == 7);
	}
    
	
	/**
	 * 添加一条信息：
	 * 1：添加一条空信息
	 * 2：添加一条字串信息
	 * 3：添加数组信息
	 * 4: 添加对象
	 */
	public function testAddMessageNomal() {
		$this->assertEquals(null, $this->message->addMessage('', 'php'));
		$this->message->addMessage('hello world', 'php');
		$this->assertEquals('hello world', $this->message->getMessage('php'));
		$this->message->addMessage('phpWind', 'php');
		$this->assertEquals('phpWind', $this->message->getMessage('php'));
	}
	
	public function testAddMessageWithArray() {
		$_tmp = array('one' => 'php', 'two' => $this->message, 'three' => '', 'four', 
						'five' => array('six' => 'true', 'seven' => 'false'));
		$this->message->addMessage($_tmp);
		$this->assertEquals('php', $this->message->getMessage('one'));
		
		$message = $this->message->getMessage('two');
		$this->assertTrue(is_object($message) && $message instanceof WindMessage);
		
		$this->assertEquals('', $this->message->getMessage('three'));
		$this->assertEquals('four', $this->message->getMessage(0));
		$this->assertEquals('true', $this->message->getMessage('six'));
		$this->assertEquals('', $this->message->getMessage('five'));
		
		$messages = $this->message->getMessage(null);
		$this->assertTrue(is_array($messages) && count($messages) == 5);
		
		$messages = $this->message->getMessage('');
		$this->assertTrue(is_array($messages) && count($messages) == 5);
	}
	
	public function testGetMessageWithArray() {
		$_tmp = array('one' => 'php', 'two' => $this->message, 'three' => '', 'four', 
						'five' => array('six' => 'true', 'seven' => 'false'));
		$this->message->addMessage($_tmp);
		$message = $this->message->getMessageWithArray();
		$this->assertTrue(is_array($message) && count($message) == 5);
		
		$message = $this->message->getMessageWithArray('seven');
		$this->assertTrue(is_array($message) && count($message) == 1 && $message[0] == 'false');
		
		$message = $this->message->getMessageWithArray(0);
		$this->assertTrue(is_array($message) && count($message) == 1 && $message[0] == 'four');
		
		$message = $this->message->getMessageWithArray(null);
		$this->assertTrue(is_array($message) && count($message) == 5);
	}
	
	public function testGetMessageWithString() {
		$_tmp = array('one' => 'php', 'six' => 'true', 'seven' => 'false', 'four');
		$this->message->addMessage($_tmp);
		$string = implode(',', $_tmp);
		$message = $this->message->getMessageWithString();
		$this->assertTrue($string == $message);
		$this->assertEquals('php', $this->message->getMessageWithString('one'));
	}
	
	public function testClear() {
		$_tmp = array('one' => 'php', 'two' => $this->message, 'three' => '', 'four', 
						'five' => array('six' => 'true', 'seven' => 'false'));
		$this->message->addMessage($_tmp);
		$this->assertEquals('php', $this->message->getMessage('one'));
		$this->message->clear('one');
		$this->assertEquals('', $this->message->getMessage('one'));
		
		$this->assertEquals('true', $this->message->getMessage('six'));
		$this->assertEquals('false', $this->message->getMessage('seven'));
		$this->assertEquals('four', $this->message->getMessage(0));
		$this->message->clear(0);
		$this->assertEquals('', $this->message->getMessage(0));
		$this->message->clear();
		$this->assertEquals('', $this->message->getMessage('one'));
		$this->assertEquals('', $this->message->getMessage('six'));
		$this->assertEquals('', $this->message->getMessage('sevem'));
		
	}
}