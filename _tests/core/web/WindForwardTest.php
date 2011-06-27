<?php

class WindForwardTest extends BaseTestCase {
	
	private $windForward;
	
	/**
	 * @param string $layout
	 * 
	 * @dataProvider providerWithLayout
	 */
	/*public function testLayout($layout) {
		$this->windForward->setLayout($layout);
		$this->assertEquals(get_class($this->windForward->getLayout()), 'WindLayout');
	}
	
	public function providerWithLayout() {
		require_once 'core/WindLayout.php';
		$args = array();
		$args[] = array(new WindLayout());
		return $args;
	}*/
	
	/**
	 * @dataProvider providerWithForwardAction
	 */
	public function testWindForwardAction($action, $actionPath, $isRedirect, $args) {
		$this->windForward->setAction($action, $actionPath, $isRedirect, $args);
		$this->assertEquals($this->windForward->getAction(), $action);
		$this->assertEquals($this->windForward->getActionPath(), $actionPath);
		if ($isRedirect)
			$this->assertEquals(get_class($this->windForward->getRedirecter()), 'WindUrlManager');
		else
			$this->assertNull($this->windForward->getRedirecter());
	}
	
	public function providerWithForwardAction() {
		$args = array();
		$args[] = array('hello', '', false, array());
		$args[] = array('hello', 'defaultControllers.indexController', false, array());
		$args[] = array('hello', 'defaultControllers.indexController', true, array());
		$args[] = array('hello', 'defaultControllers.indexController', true, array('a' => '111'));
		return $args;
	}
	
	/**
	 * @dataProvider providerWithForwardTemplate
	 */
	public function testWindForwardTemplate($templateName, $templatePath, $templateConfig) {
		$this->windForward->setTemplateName($templateName);
		$this->assertEquals($this->windForward->getTemplateName(), $templateName);
		
		$this->windForward->setTemplatePath($templatePath);
		$this->assertEquals($this->windForward->getTemplatePath(), $templatePath);
		
		$this->windForward->setTemplateConfig($templateConfig);
		$this->assertEquals($this->windForward->getTemplateConfig(), $templateConfig);
	}
	
	public function providerWithForwardTemplate() {
		$args = array();
		$args[] = array('hello', '', '');
		$args[] = array('hello', 'hello.template', '');
		$args[] = array('hello', 'hello.template', 'default');
		return $args;
	}
	
	/**
	 * @param string $vars
	 * @param string $key
	 * 
	 * @dataProvider providerWithSetVars
	 */
	public function testSetVars($vars, $key = '') {
		$this->windForward->setVars($vars, $key);
		$_vars = $this->windForward->getVars();
		$this->assertEquals('1', $_vars['test1']);
	}
	
	public function providerWithSetVars() {
		$std = new stdClass();
		$std->test1 = '1';
		$args = array();
		$args[] = array('1', 'test1');
		$args[] = array(array('test1' => '1'));
		$args[] = array($std);
		return $args;
	}
	
	protected function setUp() {
		parent::setUp();
		require_once 'core/web/WindForward.php';
		$this->windForward = new WindForward();
	}
	
	protected function tearDown() {
		parent::tearDown();
	}

}

