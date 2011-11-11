<?php
require_once 'web\WindUrlHelper.php';
/**
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindUrlHelperTest extends BaseTestCase {
	
	/**
	 * Tests WindUrlHelper::resolveAction()
	 */
	public function testResolveAction() {
		$this->assertEquals(array('action','controller','module','app',array('c' => 'c', 'b' => 'b')),
		 WindUrlHelper::resolveAction("app/module/controller/action?b=b",array('c' => 'c')));
	}

	/**
	 * Tests WindUrlHelper::createUrl()
	 */
	public function testCreateUrl() {
		$this->markTestIncomplete();
	}
	
	private function provideApp(){
		$this->front = Wind::application("long", array('web-apps' => array('long' => array('modules' => array('default' => array('controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest')))),'router' => array('config' => array('routes' => array('WindRoute' => array(
	            'class'   => 'WIND:router.route.WindRoute',
			    'default' => true,
		   ))))));
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
	}
}

