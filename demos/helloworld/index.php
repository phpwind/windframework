<?php
error_reporting(E_ALL);
define('WIND_DEBUG', 0);
require_once ('../../wind/wind.php');
Wind::application()->run();

