<?php
error_reporting(E_ALL);
define('WIND_DEBUG', 0);
require_once ('../../wind/Wind.php');
Wind::application()->run();

