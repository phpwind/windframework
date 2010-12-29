<?php

class WindUrlManagerTest extends BaseTestCase {
	private $urlManager;
	
	/**
	 * @param string $url
	 * @param array|string $urlArgs
	 * @dataProvider providerWithConstruct
	 */
	public function testConstruct($url, $urlArgs) {
		$this->urlManager = new WindUrlManager($url, $urlArgs);
		if ($urlArgs === '' && $url === '') {
			$this->assertEquals($this->urlManager->buildUrl(), '');
		} else {
			$this->assertEquals($this->urlManager->buildUrl(), 'http://localhost:80/index.php?&a=index&c=hhh');
		}
	}
	
	public function providerWithConstruct() {
		$args = array();
		$args[] = array('', '');
		$args[] = array('http://localhost:80/index.php?', array('a' => 'index', 'c' => 'hhh'));
		$args[] = array('http://localhost:80/index.php?', '&a=index&c=hhh&');
		return $args;
	}
	
	protected function setUp() {
		parent::setUp();
		require_once 'core/WindUrlManager.php';
		$urlManager = new WindUrlManager();
	}
}

