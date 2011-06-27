<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/utility/WindValidator.php');

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
	
	public function testHasIpv4(){
		$this->assertTrue($this->validate->hasIpv4("198.168.2.4") > 0);
	}
	
	public function testIsIpv4(){
		$this->assertTrue($this->validate->isIpv4("192.168.1.104"));
	}
	
	/**
	 * @dataProvider providerIpv6
	 */
	public function testHasIpv6($ipv6){
		$this->assertTrue($this->validate->hasIpv6($ipv6) > 0);
	}
	
	/**
	 * @dataProvider providerIpv6
	 */
	public function testIsIpv6($ipv6){
		$this->assertTrue($this->validate->isIpv6($ipv6));
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
	
	
	
	public  function testIsArray(){
		$this->assertTrue($this->validate->isArray(array("")) && !$this->validate->isArray("a"));
	}
	
	public  function testInArray(){
		$this->assertTrue($this->validate->inArray('ab',array("ab",'cc')) && !$this->validate->inArray("a",array()) && !$this->validate->inArray(0,array('0'),true));
	}
	
	public function testIsEmpty(){
		$this->assertTrue($this->validate->isEmpty("") && $this->validate->isEmpty(0)  && $this->validate->isEmpty(array()) &&   $this->validate->isEmpty(false));
	}
	
	public function testIsRequired(){
		$this->assertTrue(!$this->validate->isRequired(""));
	}
	
	public function testIsLegalLength(){
		$this->assertTrue($this->validate->isLegalLength("3333",3));
	}
	
	public function testHasHtmlMatch(){
		$this->assertTrue($this->validate->hasHTML("afaf<a>asdfa<b>",$matchs,true) > 0 && is_array($matchs));
	}
	
	public static function providerIpv6(){
		return array(
			array('3255:0304::FE4A:174F:5577:289C:0014'),
			array('3255::0014'),
			array('3255:304::FE4A:174F:5577:289C:0014'),
			array('0:0:0:0:0:0:10.0.0.1'),
			array('::10.0.0.1'),
		);
	}
	
}