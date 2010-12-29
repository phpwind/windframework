<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
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
	private $response;
	private $request;
	public function setUp() {
		parent::setUp();
		require_once('core/WindErrorMessage.php');
		require_once('core/WindHttpResponse.php');
		require_once('core/WindHttpRequest.php');
		require_once('core/WindWebApplication.php');
		require_once('core/WindWebDispatcher.php');
		require_once('core/WindSystemConfig.php');
		$this->init();
	}
	public function tearDown() {
		$this->errorMessage = null;
	}
	
	public function testGetAndSetError() {
		$this->assertTrue(0 == count($this->errorMessage->getError('key')));
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
	 */
	public function testSendError() {
		$this->assertNull($this->errorMessage->sendError());
		$this->errorMessage->addError('ddddd', 'name');
		try {
			$this->errorMessage->sendError();
		} catch(Exception $e) {
			return;
		}
	}
	
	/**
	 * sendError测试
	 */
	public function testSetErrorAction() {
		$this->errorMessage->setErrorAction('run', 'TestErrorController');
		$this->assertNull($this->errorMessage->sendError());
		$this->errorMessage->addError('ddddd', 'name');
		try {
			$this->errorMessage->sendError();
		} catch(Exception $e) {
			return;
		}
	}
	
	private function init() {
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$systemConfig = new WindSystemConfig($this->getConfig());
		$this->response->setData($systemConfig, 'WindSystemConfig');
		$dispatcher = new WindWebDispatcher($this->request, $this->response);
		$dispatcher->module = 'default';
		$this->response->setDispatcher($dispatcher);
		$this->errorMessage = WindErrorMessage::getInstance($this->request, $this->response);
		L::register(dirname(dirname(dirname(__FILE__))) . D_S, 'TEST');
		$this->errorMessage->clear();
	}
	private function getConfig() {
		return array(
		  'modules' => array(
			'default' => array(
				'path' => 'data',
				'template' => 'default',
				'controllerSuffix' => 'controller',
				'actionSuffix' => 'action',
				'method' => 'run',
			)));
	}
}
