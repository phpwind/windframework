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
		Wind::application()->createApplication();
		$this->assertTrue($this->testController->getRequest() instanceof WindHttpRequest);
	}

	public function testGetInput() {
		$_GET['long'] = $_POST['xxx'] = $_COOKIE['wq'] = 1;
		$data = array('long', 'xxx', 'wq');
		Wind::application()->createApplication();
		$this->assertEquals($this->testController->getInput('long', 'get'), 1);
		$this->assertEquals($this->testController->getInput('xxx', 'post'), 1);
		$this->assertEquals($this->testController->getInput('wq', 'cookie'), 1);
		$this->assertArrayEquals($this->testController->getInput($data), array_fill(0, 3, 1));
		$this->assertArrayEquals($this->testController->getInput($data, '', array($this, 'fun')), array_fill(0, 3, array(1,'shilong1')));
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
	 * @dataProvider dataForSetGlobal
	 */
	public function testSetGlobal($data, $key ='') {
		Wind::application()->createApplication();
		$this->testController->setGlobal($data, $key);
		$this->assertEquals('shilong', Wind::getApp()->getResponse()->getData('G', 'G', 'name'));
	}

	public function dataForSetGlobal() {
		$args = array();
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		return $args;
	}

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
		$this->testController->setThemePackage('package');
		$this->testController->setLayout("layout1");
		
		$this->assertEquals(array("LongController", "php", "/data", "layout1"), 
			array(
				$windView->templateName, 
				$windView->templateExt, 
				$windView->templateDir, 
				$windView->layout));
	
	}

	public function testResolveActionFilter() {
		require_once 'data/Listener.php';
		Wind::application()->createApplication();
		$errorMessage = new WindErrorMessage("shi");
		$this->testController->setErrorMessage($errorMessage);
		$forward = new WindForward();
		$forward->setAction("long");
		$this->testController->setForward($forward);
		
		$_GET['name'] = 'shilong';
		$this->testController->setGlobal('shilong', 'name');
		$forward->setVars('name', 'xxxxx');
		$_GET['wuq'] = 'wuq';
		
		$this->testController->resolveActionFilter($this->dataForResolveActionFilter());
		$this->assertEquals("post_post_post_pre_pre_pre_shi", $errorMessage->getError(0));
		$this->assertEquals("post_post_post_pre_pre_pre_long", $forward->getAction());
	}

	public function dataForResolveActionFilter() {
		$_GET['name'] = 'shilong';
		
		return array(
			array('class' => 'TEST:data.Listener', 'expression' => 'input:name==shilong'), 
			array('class' => 'TEST:data.Listener', 'expression' => 'g:name==shilong'), 
			array('class' => 'TEST:data.Listener', 'expression' => 'forward:name==xxx'), 
			array('class' => 'TEST:data.Listener', 'expression' => 'request:wuq==wuq'));
	}

	/**
	 * @dataProvider dataForResolvedActionMethod
	 */
	public function testResolvedActionMethod($action) {
		$router = new WindRouter();
		$router->setAction($action);
		try {
			$this->testController->doAction($router);
		} catch (WindException $e) {
			$this->assertEquals(WindException::ERROR_CLASS_METHOD_NOT_EXIST, $e->getCode());
			return;
		}
		$this->fail("ResolvedActionMethod Test Error");
	}

	public function dataForResolvedActionMethod() {
		$args = array();
		$args[] = array('do');
		$args[] = array('privateMethod');
		return $args;
	}
	


}


