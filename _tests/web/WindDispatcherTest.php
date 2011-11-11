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
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindDispatcher = null;
		parent::tearDown();
	}

	/**
	 * Tests WindDispatcher->dispatch()
	 */
	public function testDispatch() {
		$this->markTestIncomplete();
		
	}

}

