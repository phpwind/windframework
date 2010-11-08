<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
header("Content-type: text/html; charset=gbk");

define('R_P', dirname(__FILE__));
define('F_P', R_P . '/../../wind/');
define('C_P', R_P . '/wind/');

require_once (C_P . '/config.php');
require_once (F_P . '/wind.php');

print_r(W::$_included);

$frontController = new WFrontController();
$frontController->run();


class TestCase
{
    public $a    = 1;
    protected $b    = 2;
    private $c    = 3;

    public static function expose()
    {
        print_r(get_class_vars(__CLASS__));
    }
}

TestCase::expose();
print_r(get_class_vars('TestCase'));


