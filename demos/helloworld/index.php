<?php
require_once (dirname(__FILE__) . '/../../wind/wind.php');
$start = microtime(true);
//Wind::perLoadCoreLibrary(COMPILE_LIBRARY_PATH);
Wind::run();
echo microtime(true) - $start;
?>