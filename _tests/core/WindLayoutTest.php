<?php

class WindLayoutTest extends BaseTestCase {
	
	/**
	 * @param string $fileName
	 * @param string $dir
	 * @param string $ext
	 * @param string $contentTplName
	 * 
	 * @dataProvider providerWithLayoutFileError
	 */
	public function testParserLayoutWithError($fileName, $dir, $ext, $contentTplName) {
		$windLayout = $this->createWindLayout($fileName);
		try {
			$segments = $windLayout->parserLayout($dir, $ext, $contentTplName);
		} catch (Exception $exception) {
			$this->assertEquals(get_class($exception), 'WindException');
		}
	}
	
	/**
	 * @param string $fileName
	 * @param string $dir
	 * @param string $ext
	 * @param string $contentTplName
	 * 
	 * @dataProvider providerWithLayoutFile
	 */
	public function testParserLayout($fileName, $dir, $ext, $contentTplName) {
		$windLayout = $this->createWindLayout($fileName);
		$segments = $windLayout->parserLayout($dir, $ext, $contentTplName);
		$this->assertEquals($segments[0], 'header');
		$this->assertEquals($segments[1], 'content');
		$this->assertEquals($segments[2], 'footer');
	}
	
	public function providerWithLayoutFileError() {
		$args = array();
		$args[] = array('data.layout1', '', 'htm', 'content');
		return $args;
	}
	
	public function providerWithLayoutFile() {
		$args = array();
		$args[] = array('data.layout', '', 'htm', 'content');
		return $args;
	}
	
	private function createWindLayout($fileName) {
		require_once 'core/WindLayout.php';
		$windLayout = new WindLayout();
		$windLayout->setLayoutFile($fileName);
		return $windLayout;
	}
	
	protected function setUp() {
		parent::setUp();
	}
	
	protected function tearDown() {
		parent::tearDown();
	}

}

