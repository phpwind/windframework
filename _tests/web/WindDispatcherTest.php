<?php
/**
 * WindDispatcher test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindDispatcherTest extends BaseTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindForward.php';
	}

	/**
	 * Tests WindDispatcher->dispatch()
	 */
	public function testDispatch() {
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_FILENAME'] . '?c=long&a=noPrint';
		$front = Wind::application('long', 
			array(
				'web-apps' => array(
					'long' => array(
						'modules' => array(
							'default' => array(
								'controller-path' => 'data', 
								'controller-suffix' => 'Controller', 
								'error-handler' => 'TEST:data.ErrorControllerTest'))))))->run();
		
		$forward = new WindForward();
		$forward->setIsReAction(true);
		$forward->setAction('/long/test');
		ob_start();
		Wind::getApp()->doDispatch($forward);
		$this->assertEquals(ob_get_clean(), 'LongController-test');
	}

}

