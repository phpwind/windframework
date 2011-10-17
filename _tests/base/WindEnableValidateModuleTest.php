<?php
/**
 * WindEnableValidateModule test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class WindEnableValidateModuleTest extends BaseTestCase {
	
	private $app;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindEnableValidateModule.php';
		$this->app = Wind::application("EnableValidata", $this->getConfigData());
	}
	
	protected function tearDown(){
		Wind::resetApp();
		parent::tearDown();
	}

	public function testFormFilter(){
		$_GET['m'] = 'default';
		$_GET['c'] = 'long';
		$_GET['a'] = 'test';
		$_POST['shi'] = 'shi';
		$_POST['long'] = 'long';
		$this->app->run();
		$testForm = $this->app->getRequest()->getAttribute("testForm");
		$this->assertEquals("shi", $testForm->getShi());
		$this->assertEquals("long", $testForm->getLong());
	}
	
	private function getConfigData(){
		return array("EnableValidata" => array(
		'filters' => array(
			'filter' => array(
				'class' => 'WIND:web.filter.WindFormFilter', 
				'pattern' => 'default_long_test', 
				'form' => 'TEST:data.TestForm'
			),
			
		),
		'modules' => array(
				'default' => array(
					'controller-path' => 'data',
				)
		),  ));
	}

}

