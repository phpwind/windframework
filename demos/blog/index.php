<?php
error_reporting(E_ALL);
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('BLOG_PATH', ROOT_PATH . 'demos/blog/');
define('FRAMEWORK_PATH', ROOT_PATH . 'wind/');
define('COMPILE_PATH', BLOG_PATH .'compile/');
define('WIND_DEBUG', 1);
require_once FRAMEWORK_PATH . 'Wind.php';
Wind::register(BLOG_PATH . 'data', 'DATA');
$appName = 'blog';
$config = BLOG_PATH . 'config/config.php';
Wind::application($appName, $config)->run();