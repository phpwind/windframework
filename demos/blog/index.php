<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('WIND_DEBUG', 1);
require_once '../../wind/Wind.php';

Wind::register('template', 'TPL');
Wind::application('blog', 'config/config.php')->run();

