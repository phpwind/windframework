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
	
	public function init() {
		require_once ('component/Log/WindLogger.php');
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
		WindLogger::log($msg, $type);
	}
	
	public function testFlush() {
		$this->assertTrue(WindLogger::flush());
	}
	
	public function testInfo() {
		WindLogger::info('hello i am testing!');
	}
	
	public function testTrace() {
		WindLogger::trace('Statck trace:');
	}
	
	public function testDebug() {
		WindLogger::debug('Debug Echo ');
	}
	
	public function testError() {
		WindLogger::error('Error access');
	}
	
	public function testFlushEnd() {
		$this->assertTrue(WindLogger::flush());
	}
	
	public function testClearFiles() {
		$this->assertTrue(WindLogger::clearFiles());
	}
}