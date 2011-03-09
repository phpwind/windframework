<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */


require_once ('component/utility/WindPack.php');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindPackTest extends PHPUnit_Framework_TestCase {
	private $pack = null;
	private $path = '';
	
	public function init() {
		if (null === $this->pack) {
			$this->pack = new WindPack();
			$this->path = $this->pack->realDir($this->pack->getFilePath(dirname(dirname(__FILE__))));
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public static function providerPachMethod(){
		return array(
			array(WindPack::STRIP_SELF,true),
			array(WindPack::STRIP_PHP,true),
			array(WindPack::STRIP_TOKEN,true),
			array(WindPack::STRIP_SELF,false),
			array(WindPack::STRIP_PHP,false),
			array(WindPack::STRIP_TOKEN,false),
		);
	}
	/**
	 * @dataProvider providerPachMethod
	 */
	public function testPackFromList($packMethod,$compress){
		$fileList = array(__FILE__=>$this->pack->getFileName(__FILE__));
		$dst = $this->path."data/compile/file_{$packMethod}_{$compress}.php";
		$result = $this->pack->packFromFileList($fileList, $dst, $packMethod, $compress);
		$this->assertTrue($result && is_file($dst));
	}
	/**
	 * @dataProvider providerPachMethod
	 */
	public function testPackFromDir($packMethod,$compress){
		$dir = $this->path.'component/db/';
		$dst = $this->path."data/compile/dir_{$packMethod}_{$compress}.php";
		$result = $this->pack->packFromDir($dir, $dst, $packMethod, $compress);
		$this->assertTrue($result && is_file($dst));
	}
	
	public function testPackFromFileByCallBack(){
		$fileList = array(__FILE__=>$this->pack->getFileName(__FILE__));
		$dst = $this->path."data/compile/file_callback_pack.php";
		$this->pack->setContentInjectionCallBack(array($this, 'callback'));
		$result = $this->pack->packFromFileList($fileList, $dst, WindPack::STRIP_PHP, true);
		$this->assertTrue($result && is_file($dst));
		
	}
	
	public function callBack(){
		return 'echo 2222;';
	}
	
	
}