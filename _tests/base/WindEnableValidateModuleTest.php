<?php
/**
 * WindEnableValidateModule test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindEnableValidateModuleTest extends BaseTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindEnableValidateModule.php';
	}

	public function testFormFilter() {
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_FILENAME'] . '?c=long&a=test';
		$_POST['shi'] = 'shi';
		$_POST['long'] = 'long';
		Wind::application('EnableValidata', $this->getConfigData())->run();
		$testForm = Wind::getApp()->getRequest()->getAttribute("testForm");
		$this->assertEquals("shi", $testForm->getShi());
		$this->assertEquals("long", $testForm->getLong());
	}

	private function getConfigData() {
		return array(
			"web-apps" => array(
				"EnableValidata" => array(
					'filters' => array(
						'filter' => array(
							'class' => 'WIND:web.filter.WindFormFilter', 
							'pattern' => 'default/long/test', 
							'form' => 'TEST:data.TestForm')), 
					
					'modules' => array('default' => array('controller-path' => 'data')))));
	}

}

