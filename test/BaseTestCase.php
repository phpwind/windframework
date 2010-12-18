<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
!defined('R_P') && define('R_P', dirname(dirname(__FILE__)));
include 'PHPUnit/Framework/TestCase.php';
include R_P . DIRECTORY_SEPARATOR . 'wind/WindBase.php';
C::init(include R_P . '/test/config.php');
L::register(WIND_PATH, 'WIND');

abstract class BaseTestCase extends PHPUnit_Framework_TestCase {

}