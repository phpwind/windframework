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
class WindPageCacheTest extends BaseTestCase {
	/**
	 * @var WindPageCache
	 */
	private $pageCache = null;
	public function init() {
		$this->requireFile();
		if ($this->pageCache == null) {
			$dataDir = dirname(dirname(dirname(dirname(__FILE__)))).'/data';
			$this->pageCache = new WindPageCache();
			$this->pageCache->tplDir = $dataDir.'/cache/template/';
			$this->pageCache->htmCacheDir = $dataDir.'/cache/view/';
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function requireFile() {
		require_once ('component/cache/view/WindPageCache.php');
	}
	
	public function testPageCache(){
		$title = 'this is pageCache htm';
		$content = 'this is test,please input content in here';
		$this->assertTrue($this->pageCache->loadTpl('template.htm'));
		$content = $this->pageCache->getContent();
		$this->assertTrue(false === empty($content));
		$this->assertTrue($this->pageCache->assign('title',$title));
		$this->assertTrue($this->pageCache->assign('content',$content));
		$this->assertTrue($this->pageCache->pageStatic('static',true));
		$staticFile = $this->pageCache->htmCacheDir.'static.'.$this->pageCache->staticSuffix;
		$this->assertFileExists($staticFile);
		$this->assertTrue($this->pageCache->clearCache());
		$this->assertContains($title,$this->pageCache->getContent());
	}

}