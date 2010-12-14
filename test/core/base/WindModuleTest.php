<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import('WIND:core.base.WindModule');

class WindModuleTest extends BaseTestCase {
	private $obj;
	public function __construct() {}
	public function setUp() {
		$this->obj = new FormTest();
	}
	public function tearDown() {
		$this->obj = null;
	}
	public function testGet() {
		$this->assertEquals('hangz', $this->obj->address);
		$this->assertEquals(null, $this->obj->sex);
	}
	public function testSet() {
		$this->obj->name = 'wind';
		$this->assertEquals('wind', $this->obj->name);
		$this->obj->password = 'frameWork';
		$this->assertEquals('frameWork', $this->obj->password);
		$this->obj->address = 'china';
		$this->assertEquals('china', $this->obj->address);
	}
	public function testIsseted() {
		$this->assertFalse($this->obj->isseted('nick'));
		$this->obj->nick = 'hello';
		$this->assertTrue($this->obj->isseted('nick'));
		$this->obj->name = 'phpwind';
		$this->assertTrue($this->obj->isseted('name'));
		$this->assertEquals('phpwind', $this->obj->name);
		$this->assertFalse($this->obj->isseted('address'));
	}
}

class FormTest extends WindModule {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
}