<?php
/**
 * WindForward test case
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-14
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindForwardTest extends BaseTestCase {
	
	/**
	 * @var WindForward
	 */
	private $WindForward;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindForward.php';
		require_once 'viewer\WindView.php';
		$this->WindForward = new WindForward();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindForward = null;
		parent::tearDown();
	}

	/**
	 * Tests WindForward->forwardAction()
	 * @dataProvider dataForForwardAction
	 */
	public function testForwardAction($action, $args, $isRedirect, $immediately) {
		if ($immediately) {
			try {
				$this->WindForward->forwardAction($action, $args, $isRedirect, $immediately);
			} catch (WindForwardException $e) {
				$this->assertEquals(array($action, $args, $isRedirect, true), 
					array(
						$e->getForward()->getAction(), 
						$e->getForward()->getArgs(), 
						$e->getForward()->getIsRedirect(), 
						$e->getForward()->getIsReAction()));
				return;
			}
			$this->fail("ForwardAction Test Error!");
		} else {
			$this->WindForward->forwardAction($action, $args, $isRedirect, $immediately);
			$this->assertEquals(array($action, $args, $isRedirect, true), 
				array(
					$this->WindForward->getAction(), 
					$this->WindForward->getArgs(), 
					$this->WindForward->getIsRedirect(), 
					$this->WindForward->getIsReAction()));
		}
	}

	public function dataForForwardAction() {
		$args = array();
		$args[] = array("/default/long/shi", array('shi' => 'long'), true, false);
		$args[] = array("/hello/long/shi", array(), false, true);
		$args[] = array("/hello/shi/long", array(), true, true);
		return $args;
	}

	/**
	 * Tests WindForward->forwardRedirect()
	 */
	public function testForwardRedirect() {
		try {
			$this->WindForward->forwardRedirect("index.php");
		} catch (WindForwardException $e) {
			$this->assertEquals(array("index.php", true), 
				array($e->getForward()->getUrl(), $e->getForward()->getIsRedirect()));
			return;
		}
		$this->fail("ForwardRedirect Test Error!");
	}

	/**
	 * Tests WindForward->setVars()
	 * @dataProvider dataForSetVars
	 */
	public function testSetVars($vars, $key = '') {
		$this->WindForward->setVars($vars, $key);
		$this->assertEquals("long", $this->WindForward->getVars("shi"));
	}

	public function dataForSetVars() {
		$args = array();
		$object = new stdClass();
		$object->shi = 'long';
		$args[] = array($object);
		$args[] = array(array('shi' => 'long'));
		$args[] = array('long', 'shi');
		return $args;
	}

	/**
	 * Tests WindForward->setWindView()
	 */
	public function testSetWindView() {
		$windView = new WindView();
		$windView->setConfig(array('template-dir' => 'template/long', 'compile-dir' => 'compile/data'));
		$this->WindForward->setWindView($windView);
		$this->assertEquals($this->WindForward->getWindView(), $windView);
	}
}

