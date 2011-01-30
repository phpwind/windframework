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
		$this->assertTrue($this->validate->hasEmail("中国aoxue.1988.su.qian@163.com") > 0);
	}
	
	public function testIsEmail(){
		$this->assertTrue($this->validate->isEmail("aoxue.1988.su.qian@163.com"));
	}
	/**
	 * @dataProvider providerIdCard
	 */
	public function testHasIdCard($idCard){
		$this->assertTrue($this->validate->hasIdCard($idCard) > 0);
	}
	
	/**
	 * @dataProvider providerIdCard
	 */
	public function testIsIdCard($idCard){
		$this->assertTrue($this->validate->isIdCard($idCard));
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testHasUrl($url){
		$this->assertTrue($this->validate->hasUrl($url) > 0);
	}
	
	/**
	 * @dataProvider providerUrl
	 */
	public function testIsUrl($url){
		$this->assertTrue($this->validate->isUrl($url));
	}
	
	public function testHasChinese(){
		$this->assertTrue($this->validate->hasChinese("afa中国") > 0);
	}
	
	public function testIsChinese(){
		$this->assertTrue($this->validate->isChinese("中国"));
	}
	
	public function testHasIP(){
		$this->assertTrue($this->validate->hasIP("afa,198.168.2.4") > 0);
	}
	
	public function testIsIP(){
		$this->assertTrue($this->validate->isIP("192.168.1.104"));
	}
	
	public function testHasHTML(){
		$this->assertTrue($this->validate->hasHTML("afaf<a>asdfa<b>") > 0);
	}
	
	public function testIsHTML(){
		$this->assertTrue($this->validate->isHTML("<a>asdfa<b>"));
	}
	
	public function testHasScript(){
		$this->assertTrue($this->validate->hasScript("afaf<script>4</script>") > 0);
	}
	
	public function testIsScript(){
		$this->assertTrue($this->validate->isScript("<script type='java'>asdfa</script>"));
	}
	
	public function testIsNegative(){
		$this->assertTrue($this->validate->isNegative("-1") && !$this->validate->isNegative("1") && !$this->validate->isNegative("0"));
	}
	
	public function testIsPositive(){
		$this->assertTrue($this->validate->isPositive("1") && !$this->validate->isPositive("-1") && !$this->validate->isPositive("0"));
	}
	
	public function testIsNonNegative(){
		$this->assertTrue($this->validate->isNonNegative("0") && $this->validate->isNonNegative("1") && !$this->validate->isNonNegative("-1"));
	}
	
	public function testIsBool(){
		$this->assertTrue($this->validate->isBool(true) && !$this->validate->isBool(""));
	}
	
	public function testIsInt(){
		$this->assertTrue($this->validate->isInt(1) && !$this->validate->isInt("a"));
	}
	
	public function testIsFloat(){
		$this->assertTrue($this->validate->isFloat(10.01) && !$this->validate->isFloat("a"));
	}
	
	public  function testIsArray(){
		$this->assertTrue($this->validate->isArray(array("")) && !$this->validate->isArray("a"));
	}
	
	public function testIsEmpty(){
		$this->assertTrue($this->validate->isEmpty("") && $this->validate->isEmpty(0)  && $this->validate->isEmpty(array()) &&   $this->validate->isEmpty(false));
	}
	
	public function testIsLegalLength(){
		$this->assertTrue($this->validate->isLegalLength("3333",3));
	}
	
	public function testHasHtmlMatch(){
		$this->assertTrue($this->validate->hasHTML("afaf<a>asdfa<b>",$matchs,true) > 0 && is_array($matchs));
	}
	
	
	
	
	
	
}