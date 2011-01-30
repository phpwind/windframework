<?php

class WindViewerTest extends BaseTestCase {
	private $viewer;
	
	/**
	 * @param string $templateFile
	 * @dataProvider providerWindFetch
	 */
	public function testWindFetch($templateFile) {
		$content = $this->viewer->windFetch();
		$this->assertStringEqualsFile($templateFile, $content);
	}
	
	/**
	 * @param string $templateFile
	 * @dataProvider providerWindFetch
	 */
	public function testImmediatelyWindFetch($templateFile) {
		ob_start();
		$this->viewer->immediatelyWindFetch();
		$content = ob_get_clean();
		$this->assertStringEqualsFile($templateFile, $content);
	}
	
	public function providerWindFetch() {
		$args = array();
		$args[] = array(T_P . D_S . 'data' . D_S . 'pageTemplate.htm');
		return $args;
	}
	
	/**
	 * @dataProvider providerViewerWithLayout
	 */
	public function testViewerWithLayout($layoutFile, $templateFile) {
		$layout = new WindLayout();
		$layout->setLayoutFile($layoutFile);
		$this->viewer->setLayout($layout);
		$content = $this->viewer->windFetch();
		$this->assertStringEqualsFile($templateFile, $content);
	}
	
	public function providerViewerWithLayout() {
		$args = array();
		$args[] = array('pageLayout', T_P . D_S . 'data' . D_S . 'pageTemplate.htm');
		return $args;
	}
	
	/**
	 * @dataProvider providerWindAssign
	 */
	public function testWindAssign($vars, $key) {
		$this->viewer->windAssign($vars, $key);
		$this->assertEquals($this->viewer->getVar('test1'), '1');
		$obj = $this->viewer->getVarWithObject('pageTemplate');
		$this->assertEquals($obj->test1, '1');
	}
	
	public function providerWindAssign() {
		$std = new stdClass();
		$std->test1 = '1';
		$std->test2 = '2';
		$std->test3 = '3';
		$args = array();
		$args[] = array(array('test1' => '1', 'test2' => '2', 'test3' => '3'), '');
		$args[] = array($std, '');
		$args[] = array('1', 'test1');
		return $args;
	}
	
	public function testInitWithView() {

	}
	
	private function initViewerWithView() {
		include_once 'data/config.php';
		$view = new WindView('default');
		$view->templateDir = 'data';
		$view->templateDefault = 'pageTemplate';
		$view->templateExt = 'htm';
		$this->viewer->initWithView($view);
	}
	
	protected function setUp() {
		parent::setUp();
		require_once 'core/viewer/WindViewer.php';
		require_once 'core/viewer/WindView.php';
		require_once 'core/viewer/WindLayout.php';
		$this->viewer = new WindViewer();
		$this->initViewerWithView();
	}

}

