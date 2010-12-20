#!/usr/bin/php
<?php

/**
 * Note: Make sure you have installed PEAR and set it's path to the <include_path>
 */

require dirname(__FILE__) . '/bootstrap.php';

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

require 'PHPUnit/TextUI/Command.php';

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

PHPUnit_TextUI_Command::main();