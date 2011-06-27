<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 2011-1-21
 * @package
 */
define('LOG_PATH', COMPILE_PATH . '/log/');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 2011-1-21
 * @package
 */
class WindLoggerTest extends BaseTestCase {
	private $logger = null;
	public function init() {
		require_once ('component/Log/WindLogger.php');
		$this->logger = new WindLogger();
	}
	
	public function setUp() {
		parent::setUp();
		date_default_timezone_set('PRC');
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public static function provider() {
		return array(
			array('i am info!', 0),
			array('i am trace!', 1),
			array('debug is here!', 2),
			array('sorry, there is a error', 3),
			array('haha'),
		);
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testLog($msg, $type = 0) {
		$this->logger->log($msg, $type);
		$this->assertTrue($this->logger->flush());
	}
	
	public function testFlush() {
		$this->logger->info('hello i am testing!');
		$this->logger->trace('Statck trace:');
		$this->logger->debug('Debug Echo ');
		$this->logger->error('Error access');
		$this->assertTrue($this->logger->flush());
	}
	public function testClearFiles() {
		$this->assertTrue($this->logger->clearFiles());
	}
}