<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
!defined('R_P') && define('R_P', dirname(dirname(__FILE__)));
require_once 'PHPUnit/Framework/TestCase.php';
require_once R_P . DIRECTORY_SEPARATOR . 'wind/WindBase.php';
W::init();
C::init(include R_P . '/test/config.php');

abstract class BaseTestCase extends PHPUnit_Framework_TestCase {

}