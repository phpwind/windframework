<?php
require_once (dirname(__FILE__) . '/../../wind/wind.php');
$start = microtime(true);
W::application()->run();
echo microtime(true) - $start;
?>