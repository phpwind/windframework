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
		$front = Wind::application("WindError", array('web-apps' => array('WindError' => array('modules' => array('default' => array('controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest',
					'compile-dir' => 'data')))),'router' => array('config' => array('routes' => array('WindRoute' => array(
	            'class'   => 'WIND:router.route.WindRoute',
			    'default' => true,
		   ))))));
		$_SERVER['REQUEST_URI'] = '?shi/long/default/WindError';
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

