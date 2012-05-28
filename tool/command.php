<?php
/**
 * @author Shi Long <long.shi@alibaba-inc.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once '../wind/Wind.php';

Wind::console('command')->run();