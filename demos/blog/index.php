<?php
error_reporting(E_ALL);
require_once '../../wind/Wind.php';
Wind::application('blog', 'config/config.php')->run();