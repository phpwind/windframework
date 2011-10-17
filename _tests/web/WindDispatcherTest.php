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
	 * @var WindDispatcher
	 */
	private $WindDispatcher;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindDispatcher.php';
		require_once 'web\WindForward.php';
		$this->WindDispatcher = new WindDispatcher();
		Wind::application("dispatcherTest");
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindDispatcher = null;
		Wind::resetApp();
		parent::tearDown();
	}

	/**
	 * Tests WindDispatcher->dispatch()
	 * @dataProvider dataForDispatch
	 */
	public function testDispatch($forward, $router, $display) {
		$_SERVER['HTTP_HOST'] = 'localhost';
		//$this->WindDispatcher->dispatch($forward, $router, $display);
		
	}
	
	public function dataForDispatch(){
		$args = array();
		$router = new WindRouter();
		$router->setAction("run");
		$router->setController("index");
		$router->setModule("default");
		$forward = new WindForward();
		$forward->setIsRedirect(true);
		$forward->setUrl("index.php");
		$args[] = array($forward, $router, false);
		return $args;
	}

}

