<?php
/**
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once dirname(dirname(__DIR__)) . '/wind/Wind.php';
Wind::register(dirname(dirname(__DIR__)), 'PROJ');
Wind::application('web', __DIR__ . '/conf/config.php')->run();