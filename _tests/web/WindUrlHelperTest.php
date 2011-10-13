<?php
/**
 * WindUrlHelper test case.
 */
require_once 'web\WindUrlHelper.php';
class WindUrlHelperTest extends BaseTestCase {
	
	/**
	 * Tests WindUrlHelper::resolveAction()
	 */
	public function testResolveAction() {
		$this->assertEquals(array('action','controller','module',array('c' => 'c', 'b' => 'b')),
		 WindUrlHelper::resolveAction("/module/controller/action?b=b",array('c' => 'c')));
	}

	/**
	 * Tests WindUrlHelper::createUrl()
	 */
	public function testCreateUrl() {
		$this->provideApp();
		$this->assertEquals("http://localhost/index.php?b=b&m=module&c=controller&a=action&", 
		WindUrlHelper::createUrl("/module/controller/action",array('b' => 'b')));
	}
	
	private function provideApp(){
		$app = Wind::application();
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
	}
}

