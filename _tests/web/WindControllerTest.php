<?php
/**
 * WindController test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindControllerTest extends BaseTestCase {
	
	private $testController;

	protected function setUp() {
		parent::setUp();
		require_once 'web\WindSimpleController.php';
		require_once 'viewer\WindView.php';
		require_once 'data\LongController.php';
		$this->testController = new LongController();
		$_SERVER['REQUEST_URI'] = '?test/long/default/long';
		$this->front = Wind::application("long", array('web-apps' => array('long' => array('modules' => array('default' => array('controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest')))),'router' => array('config' => array('routes' => array('WindRoute' => array(
	            'class'   => 'WIND:router.route.WindRoute',
			    'default' => true,
		   ))))));
	}

	protected function tearDown() {
		parent::tearDown();
	}

	/**
	 * Tests WindSimpleController->setForward()
	 */
	public function testSetForward() {
		$forward = new WindForward();
		$this->testController->setForward($forward);
		$this->assertEquals($forward, $this->testController->getForward());
	}

	/**
	 * Tests WindSimpleController->setErrorMessage()
	 */
	public function testSetErrorMessage() {
		$this->testController->setErrorMessage("123");
		$this->assertEquals("123", $this->testController->getErrorMessage());
	}

	public function testGetRequest() {
		$this->markTestIncomplete();
	}

	public function testGetInput() {
		$this->markTestIncomplete();
	
	}


	public function fun($value) {
		return 'shilong' . $value;
	}

	/**
	 * 在WindForward中已测试
	 */
	public function testForwardAction() {
		try {
			$this->testController->setForward(new WindForward());
			$this->testController->forwardAction("\test", array(), true, true);
		} catch (WindForwardException $e) {
			$this->assertEquals("\test", $e->getForward()->getAction());
			return;
		}
		$this->fail("ForwardAction Test Error");
	}

	/**
	 * 在WindForward中已测试
	 */
	public function testForwardRedirect() {
		try {
			$this->testController->setForward(new WindForward());
			$this->testController->forwardRedirect("index.php");
		} catch (WindForwardException $e) {
			$this->assertEquals("index.php", $e->getForward()->getUrl());
			return;
		}
		$this->fail("ForwardRedirect Test Error");
	}

	/**
	 * 在WindForward中已测试
	 * @dataProvider dataForSetOutput
	 */
	public function testSetOutput($data, $key = '') {
		$forward = new WindForward();
		$this->testController->setForward($forward);
		$this->testController->setOutput($data, $key);
		$this->assertEquals("shilong", $forward->getVars("name"));
	}

	public function dataForSetOutput() {
		$args = array();
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		$args[] = array('shilong', 'name');
		return $args;
	}

	/**
	 */
	public function testSetGlobal() {
		$this->markTestIncomplete();
	}
/*
	public function dataForSetGlobal() {
		$args = array();
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		$args[] = array('shilong', 'name');
		return $args;
	}*/

	public function testShowMessage() {
		$errorMessage = new WindErrorMessage();
		$this->testController->setErrorMessage($errorMessage);
		try {
			$this->testController->showMessage("shilong", "name", "a");
		} catch (WindActionException $e) {
			$this->assertEquals(array("shilong", "a"), 
				array($e->getError()->getError("name"), $e->getError()->getErrorAction()));
			return;
		}
		$this->fail("ShowMessage Test Error");
	}

	public function testTemplate() {
		$forward = new WindForward();
		$windView = new WindView();
		$forward->setWindView($windView);
		$this->testController->setForward($forward);
		
		$this->testController->setTemplate("LongController");
		$this->testController->setTemplateExt("php");
		$this->testController->setTemplatePath("/data");
		$this->testController->setTheme("style");
		$this->testController->setLayout("layout1");
		
		$this->assertEquals(array("LongController", "php", "/data", "style", "layout1"), 
			array(
				$windView->templateName, 
				$windView->templateExt, 
				$windView->templateDir, 
				$windView->theme, 
				$windView->layout));
	
	}

	public function testResolveActionFilter() {
		$this->markTestIncomplete();
	}

	public function testResolvedActionMethod() {
		$this->markTestIncomplete();
	}


}


