<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/validator/WindValidator.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindValidatorTest extends BaseTestCase{
	private $validate = null;
	
	public function init(){
		if(null === $this->validate){
			$this->validate = new WindValidator();
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
		$this->assertTrue(WindValidator::hasEmail("中国aoxue.1988.su.qian@163.com") > 0);
	}
	
	public function testIsEmail(){
		$this->assertTrue(WindValidator::isEmail("aoxue.1988.su.qian@163.com"));
	}
	/**
	 * @dataProvider providerIdCard
	 */
	public function testHasIdCard($idCard){
		$this->assertTrue(WindValidator::hasIdCard($idCard) > 0);
	}
	
	/**
	 * @dataProvider providerIdCard
	 */
	public function testIsIdCard($idCard){
		$this->assertTrue(WindValidator::isIdCard($idCard));
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testHasUrl($url){
		$this->assertTrue(WindValidator::hasUrl($url) > 0);
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testIsUrl($url){
		$this->assertTrue(WindValidator::isUrl($url));
	}
	
	public function testHasChinese(){
		$this->assertTrue(WindValidator::hasChinese("afa中国") > 0);
	}
	
	public function testIsChinese(){
		$this->assertTrue(WindValidator::isChinese("中国"));
	}
	
	public function testHasIP(){
		$this->assertTrue(WindValidator::hasIP("afa,198.168.2.4") > 0);
	}
	
	public function testIsIP(){
		$this->assertTrue(WindValidator::isIP("192.168.1.104"));
	}
	
	public function testHasHTML(){
		$this->assertTrue(WindValidator::hasHTML("afaf<a>asdfa<b>") > 0);
	}
	
	public function testIsHTML(){
		$this->assertTrue(WindValidator::isHTML("<a>asdfa<b>"));
	}
	
	public function testHasScript(){
		$this->assertTrue(WindValidator::hasScript("afaf<script>4</script>") > 0);
	}
	
	public function testIsScript(){
		$this->assertTrue(WindValidator::isScript("<script type='java'>asdfa</script>"));
	}
	
	public function testIsNegative(){
		$this->assertTrue(WindValidator::isNegative("-1") && !WindValidator::isNegative("1") && !WindValidator::isNegative("0"));
	}
	
	public function testIsPositive(){
		$this->assertTrue(WindValidator::isPositive("1") && !WindValidator::isPositive("-1") && !WindValidator::isPositive("0"));
	}
	
	public function testIsNonNegative(){
		$this->assertTrue(WindValidator::isNonNegative("0") && WindValidator::isNonNegative("1") && !WindValidator::isNonNegative("-1"));
	}
	
	public function testIsBool(){
		$this->assertTrue(WindValidator::isBool(true) && !WindValidator::isBool(""));
	}
	
	public function testIsInt(){
		$this->assertTrue(WindValidator::isInt(1) && !WindValidator::isInt("a"));
	}
	
	public function testIsFloat(){
		$this->assertTrue(WindValidator::isFloat(10.01) && !WindValidator::isFloat("a"));
	}
	
	public  function testIsArray(){
		$this->assertTrue(WindValidator::isArray(array("")) && !WindValidator::isArray("a"));
	}
	
	public function testIsEmpty(){
		$this->assertTrue(WindValidator::isEmpty("") && WindValidator::isEmpty(0)  && WindValidator::isEmpty(array()) &&   WindValidator::isEmpty(false));
	}
	
	public function testIsLegalLength(){
		$this->assertTrue(WindValidator::isLegalLength("3333",3));
	}
	
	public function testHasHtmlMatch(){
		$this->assertTrue(WindValidator::hasHTML("afaf<a>asdfa<b>",$matchs,true) > 0 && is_array($matchs));
	}
	
	
	
	
	
	
}