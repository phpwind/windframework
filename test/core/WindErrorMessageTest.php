<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import('WIND:core.WindErrorMessage');

/**
 * 测试WindErrorMessage
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindErrorMessageTest extends BaseTestCase {
	private $errorMessage;
	public function setUp() {
		$this->errorMessage = WindErrorMessage::getInstance();
		$this->errorMessage->clear();
	}
	public function tearDown() {
		$this->errorMessage = null;
	}
	/**
	 * 测试获得实例
	 */
	public function testGetInstance() {
		$obj = WindErrorMessage::getInstance();
		$this->assertTrue(is_object($obj) && $obj instanceof WindErrorMessage);
	}
	
	public function testGetAndSetError() {
		$this->assertEquals('', $this->errorMessage->getError('name'));
		$this->errorMessage->addError('nameError', 'name');
		$this->assertEquals('nameError', $this->errorMessage->getError('name'));
		$this->errorMessage->addError('', 'password');
		$this->assertEquals('', $this->errorMessage->getError('password'));
		$this->errorMessage->addError(array('false', 'true'), 'flag');
		$this->assertEquals('false', $this->errorMessage->getError(0));
		$this->assertEquals('true', $this->errorMessage->getError(1));
		$this->assertTrue(is_array($this->errorMessage->getError()) && count($this->errorMessage->getError()) == 3);
	}
	/**
	 * sendError测试
	 * 待完善
	 */
	public function testSendErrorNoneAction() {
		$this->assertEquals(null, $this->errorMessage->sendError());
		$this->errorMessage->addError('nameError', 'name');
		throw new PHPUnit_Framework_IncompleteTestError('No complete');
		/*$obj->sendError();*/
	//	$this->assertEquals(null, $obj->sendError());
	}
	
	public function testSendErrorAction() {
		$this->errorMessage->setErrorAction(R_P . '/test/component/message/TestClass.php');
		$this->assertEquals(null, $this->errorMessage->sendError());
		throw new PHPUnit_Framework_IncompleteTestError('No complete');
		/*$obj->addError('nameError', 'name');
		$obj->sendError();*/
	//	$this->assertEquals(null, $obj->sendError());
	}
}
