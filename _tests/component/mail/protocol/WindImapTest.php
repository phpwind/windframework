<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * WindImap单元测试
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindImapTest extends BaseTestCase {
	private $imap = null;
	
	public function init() {
		require_once ('component/mail/protocol/WindImap.php');
		if (null === $this->imap) {
			$this->imap = new WindImap('imap.163.com', 143);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	/**
	 * @dataProvider providerLogin
	 */
	public function testOpen(){
		$this->assertContains('OK',$this->imap->open());
	}
	/**
	 *@dataProvider providerLogin
	 */
	public function testLogin($username,$password){
		$this->assertContains('OK',$this->imap->open());
		$this->assertContains('OK',$this->imap->login($username,$password));
	}
	
	public function testCreate(){
		$this->login();
		$this->assertContains('OK',$this->imap->create('test'));
	}
	
	public function testDelete(){
		$this->login();
		$this->assertContains('OK',$this->imap->delete('test'));
	}
	
	public function testRename(){
		$this->login();
		$this->assertContains('OK',$this->imap->create('test'));
		$this->assertContains('OK',$this->imap->rename('test','tests'));
		$this->assertContains('OK',$this->imap->delete('tests'));
	}
	
	/**
	 * @dataProvider providerList
	 */
	public function testFolderOfmail($base,$template){
		$this->login();
		$this->assertContains('OK',$this->imap->folderOfMail($base,$template));
	}
	
	public function testSelect(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
	}
	
	/**
	 * @dataProvider providerFetchs
	 */
	public function testFetch($mail,$fetch){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetch($mail,$fetch));
	}
	
	public function testFetchHeader(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchHeader(1));
	}
	/**
	 * @dataProvider providerHeaderFields
	 */
	public function testFetchHeaderFields($mail,$fields){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchHeaderFields($mail,$fields));
	}
	
	/**
	 * @dataProvider providerHeaderFields
	 */
	public function testFetchHeaderNotFields($mail,$fields){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchHeaderNotFields($mail,$fields));
	}
	
	public function testFetchText(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchText(1));
	}
	
	public function testFetchSection(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchBySection(1,WindImap::HEADER));
	}
	
	public function testFetchPartialOfSection(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->fetchPartialOfSection(1,2,10));
	}
	
	
	public function testStore(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->store(1));
	}
	
	public function testStripStore(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->stripStore(1));
	}
	
	public function testExamine(){
		$this->login();
		$this->assertContains('OK',$this->imap->examine('inbox'));
	}
	
	public function testExpunge(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->expunge());
	}
	
	public function testSubscribe(){
		$this->login();
		$this->assertContains('OK',$this->imap->subscribe('new'));
	}
	
	public function testUnsubscribe(){
		$this->login();
		$this->assertContains('OK',$this->imap->unsubscribe('new'));
	}
	/**
	 * @dataProvider providerList
	 */
	public function testLsub($base,$template){
		$this->login();
		$this->assertContains('OK',$this->imap->folderOfMail($base,$template));
	}
	/**
	 * @dataProvider providerStatus
	 */
	public function testStatus($mailbox,$status){
		$this->login();
		$this->assertContains('OK',$this->imap->status($mailbox,$status));
	}
	/**
	 * @dataProvider providerSearch
	 */
	public function testSearch($criteria,$value){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->search($criteria,$value));
	}
	
	
	public function testCapability(){
		$this->login();
		$this->assertContains('OK',$this->imap->capability());
	}
	
	public function testCopy(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->copy(1,'phpwind'));
	
	}
	
	public function testClose(){
		$this->login();
		$this->assertContains('OK',$this->imap->select('inbox'));
		$this->assertContains('OK',$this->imap->check());
		$this->assertContains('OK',$this->imap->close('inbox'));
		
	}
	
	public function testLogOut(){
		//$this->login();
		//$this->assertContains('OK',$this->imap->logout());
	}
	public function login() {
		$this->imap->open();
		$login = self::providerLogin();
		$this->imap->login($login[0][0], $login[0][1]);
	}
	
	public static function providerSearch(){
		return array(
			array('KEYWORD','test'),
			array('SUBJECT','a'),
			array('ALL',null)
		);
	}
	public static function providerStatus(){	
		return array(
			array('inbox','MESSAGES'),
			array('inbox',array('MESSAGES','RECENT'))
		);
	}
	public static function providerHeaderFields(){
		return array(
			array(1,'Subject'),
			array(1,array('Subject,Date')),
			array(1,array('Subject,Date,Content-Type'))
		);
	}
	
	public static function providerFetchs(){
		return array(
			array(1,'ALL'),
			array('1:2','BODY'),
			array(1,'BODYSTRUCTUR'),
			array(1,'FULL'),
			array(1,'FAST')
		);
	}
	
	
	public static function providerList(){
		return array(
			array('/','*')
		);
	}
	public static function providerLogin() {
		return array(array('aoxue.1988.su.qian@163.com', '13117146484@suSU'));
	}
}