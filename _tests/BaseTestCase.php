<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include 'PHPUnit/Framework/TestCase.php';
include 'WindBase.php';
abstract class BaseTestCase extends PHPUnit_Framework_TestCase {
	protected function setUp() {
		parent::setUp();
		W::init();
		C::init(include 'config.php');
	}
	
	protected function tearDown() {
		parent::tearDown();
		C::init(include 'config.php');
	}
}