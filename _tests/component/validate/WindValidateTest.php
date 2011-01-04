<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/validate/WindValidate.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindValidateTest extends BaseTestCase{
	private $validate = null;
	
	public function init(){
		if(null === $this->validate){
			$this->validate = new WindValidate();
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
		$this->assertTrue(WindValidate::hasEmail("中国aoxue.1988.su.qian@163.com") > 0);
	}
	
	public function testIsEmail(){
		$this->assertTrue(WindValidate::isEmail("aoxue.1988.su.qian@163.com"));
	}
	/**
	 * @dataProvider providerIdCard
	 */
	public function testHasIdCard($idCard){
		$this->assertTrue(WindValidate::hasIdCard($idCard) > 0);
	}
	
	/**
	 * @dataProvider providerIdCard
	 */
	public function testIsIdCard($idCard){
		$this->assertTrue(WindValidate::isIdCard($idCard));
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testHasUrl($url){
		$this->assertTrue(WindValidate::hasUrl($url) > 0);
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testIsUrl($url){
		$this->assertTrue(WindValidate::isUrl($url));
	}
	
	public function testHasChinese(){
		$this->assertTrue(WindValidate::hasChinese("afa中国") > 0);
	}
	
	public function testIsChinese(){
		$this->assertTrue(WindValidate::isChinese("中国"));
	}
	
	public function testHasIP(){
		$this->assertTrue(WindValidate::hasIP("afa,198.168.2.4") > 0);
	}
	
	public function testIsIP(){
		$this->assertTrue(WindValidate::isIP("192.168.1.104"));
	}
	
	public function testHasHTML(){
		$this->assertTrue(WindValidate::hasHTML("afaf<a>asdfa<b>") > 0);
	}
	
	public function testIsHTML(){
		$this->assertTrue(WindValidate::isHTML("<a>asdfa<b>"));
	}
	
	public function testHasScript(){
		$this->assertTrue(WindValidate::hasScript("afaf<script>4</script>") > 0);
	}
	
	public function testIsScript(){
		$this->assertTrue(WindValidate::isScript("<script type='java'>asdfa</script>"));
	}
	
	public function testIsNegative(){
		$this->assertTrue(WindValidate::isNegative("-1") && !WindValidate::isNegative("1") && !WindValidate::isNegative("0"));
	}
	
	public function testIsPositive(){
		$this->assertTrue(WindValidate::isPositive("1") && !WindValidate::isPositive("-1") && !WindValidate::isPositive("0"));
	}
	
	public function testIsNonNegative(){
		$this->assertTrue(WindValidate::isNonNegative("0") && WindValidate::isNonNegative("1") && !WindValidate::isNonNegative("-1"));
	}
	
	public function testIsBool(){
		$this->assertTrue(WindValidate::isBool(true) && !WindValidate::isBool(""));
	}
	
	public function testIsInt(){
		$this->assertTrue(WindValidate::isInt(1) && !WindValidate::isInt("a"));
	}
	
	public function testIsFloat(){
		$this->assertTrue(WindValidate::isFloat(10.01) && !WindValidate::isFloat("a"));
	}
	
	public  function testIsArray(){
		$this->assertTrue(WindValidate::isArray(array("")) && !WindValidate::isArray("a"));
	}
	
	public function testIsEmpty(){
		$this->assertTrue(WindValidate::isEmpty("") && WindValidate::isEmpty(0)  && WindValidate::isEmpty(array()) &&   WindValidate::isEmpty(false));
	}
	
	public function testIsLegalLength(){
		$this->assertTrue(WindValidate::isLegalLength("3333",3));
	}
	
	public function testHasHtmlMatch(){
		$this->assertTrue(WindValidate::hasHTML("afaf<a>asdfa<b>",$matchs,true) > 0 && is_array($matchs));
	}
	
	
	
	
	
	
}