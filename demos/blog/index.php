<?php
error_reporting(E_ALL);
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
require_once ROOT_PATH . 'wind/Wind.php';
Wind::application('blog', ROOT_PATH . 'demos/blog/config/config.php')->run();