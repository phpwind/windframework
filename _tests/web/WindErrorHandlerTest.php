<?php
/**
 * WindErrorHandler test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindErrorHandlerTest extends BaseTestCase {

	public function testErrorHandler() {
		$front = Wind::application("WindError", 
			array(
				'web-apps' => array(
					'WindError' => array(
						'modules' => array(
							'default' => array(
								'controller-path' => 'data', 
								'controller-suffix' => 'Controller', 
								'error-handler' => 'TEST:data.ErrorControllerTest', 
								'compile-dir' => 'data'))))));
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_FILENAME'] . '?a=shi&c=long';
		try {
			$front->run();
		} catch (Exception $e) {
			$this->assertEquals("error handled", $e->getMessage());
			return;
		}
		$this->fail("Error Handler Test Error");
	}

	protected function tearDown() {
		parent::tearDown();
	}
}

