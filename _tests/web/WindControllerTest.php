<?php
/**
 * WindSimpleController test case.
 */
class WindControllerTest extends BaseTestCase {

	private $testController;
	
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindSimpleController.php';
		require_once 'viewer\WindView.php';
		require_once 'data\LongController.php';
		$this->testController = new LongController();
		Wind::application("simpleController");
	}
	
	protected function tearDown(){
		Wind::resetApp();
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
	
	public function testGetRequest(){
		$this->assertTrue("WindHttpRequest" == get_class($this->testController->getRequest()));
	}
	
	/**
	 *@dataProvider dataForGetInput
	 */
	public function testGetInput($name, $type, $callback){
		$_GET['get'] = 'get_value';
		$_GET['get1'] = 'get1_value';
		$_POST['post'] = 'post_value';
		$_COOKIE['cookie'] = 'cookie_value';
		
		if (is_array($name)) {
			$this->assertEquals(array('get' => 'get_value', 'get1' => 'get1_value'), $this->testController->getInput($name, $type, $callback));
			return ;
		}
		if ($callback){
			list($value, $callValue) = $this->testController->getInput($name, $type, $callback);
			$this->assertEquals($name . "_value", $value);
			$this->assertEquals('shilong'.$value, $callValue);
		} else {
			$this->assertEquals($name."_value", $this->testController->getInput($name, $type, $callback));
		}

	}
	
	public function testGetInputWithObject(){
		$form = new stdClass();
		$form->name = 'shilong';
		$this->testController->getRequest()->setAttribute($form);
		$this->assertEquals("shilong", $this->testController->getInput('name', '', null));
	}
	
	public function dataForGetInput(){
		$args = array();
		$args[] = array("get", "GET", null);
		$args[] = array("post", "POST", null);
		$args[] = array("cookie", "COOKIE",null);
		$args[] = array(array("get", "get1"), "GET", null);
		$args[] = array("get", "GET", array($this, "fun"));
		$args[] = array("post", "POST", array($this, "fun"));
		$args[] = array("cookie", "COOKIE",array($this, "fun"));
		return $args;
	}
	
	public function fun($value){
		return 'shilong'.$value;
	}
	/**
	 * 在WindForward中已测试
	 */
	public function testForwardAction(){
		try {
			$this->testController->setForward(new WindForward());
			$this->testController->forwardAction("\test", array(), true, true);
		} catch (WindForwardException $e) {
			$this->assertEquals("\test", $e->getForward()->getAction());
			return ;
		}
		$this->fail("ForwardAction Test Error");
	}
	
	/**
	 * 在WindForward中已测试
	 */
	public function testForwardRedirect(){
		try {
			$this->testController->setForward(new WindForward());
			$this->testController->forwardRedirect("index.php");
		} catch (WindForwardException $e) {
			$this->assertEquals("index.php", $e->getForward()->getUrl());
			return ;
		}
		$this->fail("ForwardRedirect Test Error");
	}
	
	/**
	 * 在WindForward中已测试
	 * @dataProvider dataForSetOutput
	 */
	public function testSetOutput($data, $key = ''){
		$forward = new WindForward();
		$this->testController->setForward($forward);
		$this->testController->setOutput($data, $key);
		$this->assertEquals("shilong", $forward->getVars("name"));
	}
	
	public function dataForSetOutput(){
		$args = array();
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		$args[] = array('shilong', 'name');
		return $args;
	}
	
	/**
	 * @dataProvider dataForSetOutput
	 */
	public function testSetGlobal($data, $key = ''){
		$this->testController->setGlobal($data, $key);
		$vars = $this->testController->getResponse()->getData();
		$this->assertEquals("shilong", $vars['G']['name']);
	}
	
	public function dataForSetGlobal(){
		$args = array();
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		$args[] = array('shilong', 'name');
		return $args;
	}
	
	public function testShowMessage(){
		$errorMessage = new WindErrorMessage();
		$this->testController->setErrorMessage($errorMessage);
		try {
			$this->testController->showMessage("shilong", "name", "a");
		} catch (WindActionException $e) {
			$this->assertEquals(array("shilong", "a"),array($e->getError()->getError("name"), $e->getError()->getErrorAction()));
			return ;
		}
		$this->fail("ShowMessage Test Error");
	}
	
	public function testTemplate(){
		$forward = new WindForward();
		$windView = new WindView();
		$forward->setWindView($windView);
		$this->testController->setForward($forward);
		
		$this->testController->setTemplate("LongController");
		$this->testController->setTemplateExt("php");
		$this->testController->setTemplatePath("/data");
		$this->testController->setTheme("style");
		$this->testController->setLayout("layout1");
		
		$this->assertEquals(array("LongController", "php", "/data", "style", "layout1"), array(
			$windView->templateName, $windView->templateExt, $windView->templateDir, $windView->theme, $windView->layout,
		));
		
	}
	
	public function testResolveActionFilter(){
		require_once 'data/Listener.php';
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
	
	public function dataForResolveActionFilter(){
		$_GET['name'] = 'shilong';
		
		return array(array('class' => 'TEST:data.Listener', 'expression' => 'input:name==shilong'),
					 array('class' => 'TEST:data.Listener', 'expression' => 'g:name==shilong'),
					 array('class' => 'TEST:data.Listener', 'expression' => 'forward:name==xxx'),
					 array('class' => 'TEST:data.Listener', 'expression' => 'request:wuq==wuq')
					 );
	}
	
	/**
	 * @dataProvider dataForResolvedActionMethod
	 */
	public function testResolvedActionMethod($action){
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
	
	public function dataForResolvedActionMethod(){
		$args = array();
		$args[] = array('do');
		$args[] = array('privateMethod');
		return $args;
	}

}


