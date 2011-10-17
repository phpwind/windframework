<?php
/**
 * WindActionException test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindActionExceptionTest extends BaseTestCase {

	/**
	 * @var WindErrorMessage
	 */
	private $errorMessage;
	
	public function setUp(){
		parent::setUp();
		require_once 'base\WindActionException.php';
		require_once 'base\WindErrorMessage.php';
		$this->errorMessage = new WindErrorMessage("error","errorAction");
	}
	
	public function tearDown(){
		parent::tearDown();
	}
	
	/**
	 * Tests WindActionException->__construct()
	 */
	public function test__construct() {
		try {
			$this->errorMessage->sendError();
		}catch (WindActionException $e){
			$this->assertEquals($e->getError(),$this->errorMessage);
			return;
		}
		try {
			throw new WindActionException("error!");
		}catch (Exception $e){
			$this->assertEquals($e->getMessage(), "error!");
			return;
		}
		$this->fail("WindActionExceptionTest Error");
	}
	
}

