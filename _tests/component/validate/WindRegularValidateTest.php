<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/validate/WindRegularValidate.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindRegularValidateTest extends PHPUnit_Framework_TestCase{
	private $validate = null;
	
	public function init(){
		if(null === $this->validate){
			$this->validate = new WindRegularValidate();
		}
	}
	
	public function setUp(){
		parent::setUp();
		$this->init();
	}
	
	public static function providerIdCard(){
		return array(
			array('422801198805124022'),
			array('422801198805124'),
			array('42280119880512402X')
		);
	}
	
	public static function providerUrl(){
		return array(
			array("http://www.baidu.com"),
			array("http://www.baidu.com:80/a"),
			array("http://www.baidu.com/a/b.php?uid=2&c=a"),
			array("https://www.baidu.com/")
		);
	}
	
	
	public function testHasEmail(){
		$this->assertTrue(WindRegularValidate::hasEmail("中国aoxue.1988.su.qian@163.com") > 0);
	}
	
	public function testIsEmail(){
		$this->assertTrue(WindRegularValidate::isEmail("aoxue.1988.su.qian@163.com"));
	}
	/**
	 * @dataProvider providerIdCard
	 */
	public function testHasIdCard($idCard){
		$this->assertTrue(WindRegularValidate::hasIdCard($idCard) > 0);
	}
	
	/**
	 * @dataProvider providerIdCard
	 */
	public function testIsIdCard($idCard){
		$this->assertTrue(WindRegularValidate::isIdCard($idCard));
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testHasUrl($url){
		$this->assertTrue(WindRegularValidate::hasUrl($url) > 0);
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testIsUrl($url){
		$this->assertTrue(WindRegularValidate::isUrl($url));
	}
	
	public function testHasChinese(){
		$this->assertTrue(WindRegularValidate::hasChinese("afa中国") > 0);
	}
	
	public function testIsChinese(){
		$this->assertTrue(WindRegularValidate::isChinese("中国"));
	}
	
	public function testHasIP(){
		$this->assertTrue(WindRegularValidate::hasIP("afa,198.168.2.4") > 0);
	}
	
	public function testIsIP(){
		$this->assertTrue(WindRegularValidate::isIP("192.168.1.104"));
	}
	
	public function testHasHTML(){
		$this->assertTrue(WindRegularValidate::hasHTML("afaf<a>asdfa<b>") > 0);
	}
	
	public function testIsHTML(){
		$this->assertTrue(WindRegularValidate::isHTML("<a>asdfa<b>"));
	}
	
	public function testHasScript(){
		$this->assertTrue(WindRegularValidate::hasScript("afaf<script>4</script>") > 0);
	}
	
	public function testIsScript(){
		$this->assertTrue(WindRegularValidate::isScript("<script type='java'>asdfa</script>"));
	}
	
	public function testIsNegative(){
		$this->assertTrue(WindRegularValidate::isNegative("-1") && !WindRegularValidate::isNegative("1") && !WindRegularValidate::isNegative("0"));
	}
	
	public function testIsPositive(){
		$this->assertTrue(WindRegularValidate::isPositive("1") && !WindRegularValidate::isPositive("-1") && !WindRegularValidate::isPositive("0"));
	}
	
	public function testIsNonNegative(){
		$this->assertTrue(WindRegularValidate::isNonNegative("0") && WindRegularValidate::isNonNegative("1") && !WindRegularValidate::isNonNegative("-1"));
	}
	
	
	
	
	
	
}