<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSocketTest extends BaseTestCase{
	private $socket = null;
	
	public function init(){
		L::import ( 'WIND:component.mail.protocol.WindSocket' );
		if(null === $this->socket){
			$url = 'www.baidu.com';
			$this->socket = new WindSocket($url,80);
		}
	}
	
	public function setUp(){
		parent::setUp();
		$this->init();
	}
	
	public function tearDown(){
		parent::tearDown();
	}
	
	public function testOpen(){
		$this->assertTrue(!is_resource($this->socket->getSocket()));
		$this->socket->open();
		$this->assertTrue(is_resource($this->socket->getSocket()));
	}
	
	public function testClose(){
		$this->socket->open();
		$this->assertTrue($this->socket->close());
	}
	
	public function testRequest(){
		$this->assertTrue( 0 < (int)($this->request()));
	}
	
	public function testResponse(){
		$this->request();
		$this->assertTrue(0 < strlen($this->socket->response()));
	}
	
	public function testResponseLine(){
		$this->request();
		$this->assertTrue(0 < strlen($this->socket->responseLine()));
	}
	
	public function testSetSocketTimeOut(){
		$this->socket->open();
		$this->assertTrue($this->socket->setSocketTimeOut(8));
	}
	
	public function request(){
		$this->socket->open();
		$request  = "GET http://www.baidu.com HTTP/1.1\n";
		$request .= "Host: www.baidu.com\n";
		$request .= "Connection: Close\n";
		$request .= "\n";
		return $this->socket->request($request);
	}
}