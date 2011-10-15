<?php
/**
 * WindErrorHandler test case.
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-14
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindErrorHandlerTest extends BaseTestCase {

	public function testErrorHandler() {
		$app = Wind::application("long", 
			array(
				'long' => array(
					'modules' => array(
						'long' => array(
							'error-handler' => 'TEST:data.ErrorControllerTest', 
							'controller-path' => 'data', 
							'compile-dir' => 'data')))));
		$_GET['m'] = 'long';
		$_GET['c'] = 'long';
		$_GET['a'] = 'shi';
		try {
			$app->run();
		} catch (Exception $e) {
			$this->assertEquals("error handled", $e->getMessage());
			return;
		}
		$this->fail("Error Handler Test Error");
	}

	protected function tearDown() {
		Wind::resetApp();
		parent::tearDown();
	}
}

