<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
require_once('core/base/WindModule.php');

/**
 * WindModule单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
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
	public function testToArray() {
		$this->obj->name = 'wind';
		$this->obj->nick = 'hello';
		$this->obj->password = 'phpwind';
		$value = $this->obj->toArray();
		$this->assertTrue(is_array($value) && count($value) == 4);
		$this->assertTrue(isset($value['name']) && $value['name'] == 'wind');
		$this->assertTrue(isset($value['nick']) && $value['nick'] == 'hello');
		$this->assertTrue(isset($value['password']) && $value['password'] == 'phpwind');
		$this->assertTrue(isset($value['address']) && $value['address'] == 'hangz');
	}
}

class FormTest extends WindModule {
	protected $name;
	protected $password;
	protected $address = 'hangz';
	protected $nick;
}