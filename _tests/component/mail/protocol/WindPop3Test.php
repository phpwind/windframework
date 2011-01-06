<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindPop3Test extends BaseTestCase {
	private $pop3 = null;
	
	public function init() {
		require_once ('component/mail/protocol/WindPop3.php');
		if (null === $this->pop3) {
			$this->pop3 = new WindPop3('pop.qq.com', 110);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testOpen(){
		$this->assertTrue(is_string($this->pop3->open()));
	}
	
	/**
	 * @dataProvider providerLogin
	 */
	public function testLogin($username,$passwrod){
		$this->pop3->open();
		$this->assertContains('OK',$this->pop3->login($username,$passwrod));
	}
	
	public function testStat(){
		$this->login();
		$this->assertTrue(is_array($this->pop3->stat()));
	}
	
	public function testUidl(){
		$this->login();
		$this->assertTrue(is_array($this->pop3->uidl()));
		$this->assertTrue(is_array($this->pop3->uidl(1)));
	}
	
	public function testGetList(){
		$this->login();
		$this->assertTrue(is_array($this->pop3->getList()));
		$this->assertTrue(is_array($this->pop3->getList(1)));
	}
	
	public function testRetr(){
		$this->login();
		$str = $this->pop3->retr(1);
		$this->assertTrue(is_string($str));
		$str = $this->pop3->getMailContent($str);
		$this->assertTrue(is_array($str) && count($str) === 2);
		
	}
	
	public function testDele(){
		$this->login();
		$this->assertContains('OK',$this->pop3->dele(1));
		//$this->assertContains('Bye',$this->pop3->quit());
	}
	
	public function testRset(){
		$this->login();
		$this->assertContains('OK',$this->pop3->dele(1));
		$this->assertContains('OK',$this->pop3->rset(1));
	}
	
	public function testTop(){
		$this->login();
		$this->assertTrue(is_string($this->pop3->top(1)));
		$this->assertTrue(is_string($this->pop3->top(1,2)));
		$this->assertContains('OK',$this->pop3->noop());
		//$this->assertContains('Bye',$this->pop3->quit());
		
	}
	
	
	
	public  function login(){
		$this->pop3->open();
		$login = self::providerLogin();
		$this->pop3->login($login[0][0],$login[0][1]);
	}
	
	
	public static function providerLogin(){
		return array(
			 array('635927818@qq.com','password')
		);
	}
}