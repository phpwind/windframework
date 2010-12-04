<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class IndexController extends WindController {
	const AA= 'aaa';
	public function run() {
		echo IWindRequest::COOKIE;
		echo self::AA;
		echo "mmmmm";
	}

}