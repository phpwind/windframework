<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindCacheFactoryTest extends BaseTestCase{
	/**
	 * @var WindCacheFactory
	 */
	private $cacheFactory = null;
	public function init() {
		require_once ('component/cache/windcachefactory.php');
		if ($this->cacheFactory == null) {
			$this->cacheFactory = new WindCacheFactory();
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testViewCacheFactory(){
		$cache = $this->cacheFactory->viewFactory('WindPageCache');
		$this->assertThat($cache,$this->isInstanceOf('WindPageCache'));
	}
	
	public function testDependencyFactory(){
		$dependency = $this->cacheFactory->dependencyFactory('WindCacheDependency');
		$this->assertThat($dependency,$this->isInstanceOf('WindCacheDependency'));
	}
	
	public function testStoredCacheFactory(){
		$cacheDir = dirname(dirname(dirname(__FILE__))).'/data/';
		$cache = $this->cacheFactory->storedFactory('WindFileCache',array('cachedir'=>$cacheDir));
		$this->assertThat($cache,$this->isInstanceOf('WindFileCache'));
	}
	
	public function testViewCacheFactoryReload(){
		$cache = $this->cacheFactory->viewFactory('WindPageCache',array(),true);
		$this->assertThat($cache,$this->isInstanceOf('WindPageCache'));
	}
	
	public function testViewCacheFactoryTowLoad(){
		$cache = $this->cacheFactory->viewFactory('WindPageCache');
		$cache = $this->cacheFactory->viewFactory('WindPageCache');
		$this->assertThat($cache,$this->isInstanceOf('WindPageCache'));
	}
	
	
}