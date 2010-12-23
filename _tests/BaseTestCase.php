<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
(!class_exists('PHPUnit_Framework_TestCase')) && include 'PHPUnit/Framework/TestCase.php';
include 'WindBase.php';
W::init();
abstract class BaseTestCase extends PHPUnit_Framework_TestCase {
	protected function setUp() {
		parent::setUp();
		C::init(include 'config.php');
	}
	
	protected function tearDown() {
		parent::tearDown();
	}
}