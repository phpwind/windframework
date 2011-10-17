<?php
require_once 'base\WindException.php';
/**
 * WindException test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindExceptionTest extends BaseTestCase {

	/**
	 * Tests WindException->__construct()
	 */
	public function test__construct() {
		try {
			throw new WindException('method1',WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		} catch (Exception $e) {
			$this->assertEquals("WindException",get_class($e));
			$this->assertEquals(
				"Unable to access the method 'method1' in current class , the method is not exist or is protected.", 
				$e->getMessage());
		}
	}

}

