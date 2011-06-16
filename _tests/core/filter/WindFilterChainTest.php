<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindFilterChainTest extends BaseTestCase {
	private $filterChain = null;
	public function setUp() {
		parent::setUp();
		require_once ('core/filter/WindFilterChain.php');
		require_once ('core/config/WindConfig.php');
		Wind::register(T_P, 'TEST', false);
		$this->filterChain = new WindFilterChain($this->getFilterConfig());
	}
	public function tearDown() {
		parent::tearDown();
	}
	private function getFilterConfig() {
		$filters = array('TestFilter' => array('class' => 'TEST:data.ForWindFilter'), 
			'TestChain' => array('class' => 'TEST:core.filter.WindFilterChainTest'));
		return $filters;
	}
	
	public function testGetHandle() {
		$handle = $this->filterChain->getHandler();
		$this->assertTrue($handle instanceof ForWindFilter);
		
		$handle = $this->filterChain->getHandler();
		$this->assertTrue($handle instanceof WindHandlerInterceptor);
		
		$this->assertNull($this->filterChain->getHandler());
	}
	
	public function show() {
		echo 'i am excute operator!';
		return 1;
	}
	public function testHandle() {
		$this->filterChain->setCallBack(array($this, 'show'));
		$handle = $this->filterChain->getHandler();
		ob_start();
		$result = $handle->handle(1, '2');
		$tmp = ob_get_clean();
		$this->assertEquals(1, $result);
		$except = "1+2i am excute operator!\$a=1,\$b=2";
		$this->assertEquals($tmp, $except);
	}
	
	public function testDeleteFiler() {
		$this->filterChain->setCallBack(array($this, 'show'));
		$this->filterChain->deleteFilter('TestChain');
		$this->filterChain->deleteFilter('TestFilter');
		$handle = $this->filterChain->getHandler();
		ob_start();
		$result = $handle->handle(2);
		$tmp = ob_get_clean();
		$this->assertEquals(1, $result);
		$except = "i am excute operator!";
		$this->assertEquals($tmp, $except);
	
	}
	
	public function testAddFiler() {
		$this->filterChain->setCallBack(array($this, 'show'));
		$this->filterChain->deleteFilter('TestFilter');
		$this->filterChain->addFilter(new ForWindFilter());
		$this->filterChain->addFilter(new WindFilterChainTest(), 'ForWindFilter');
		$handle = $this->filterChain->getHandler();
		ob_start();
		$result = $handle->handle('php');
		$tmp = ob_get_clean();
		$this->assertEquals(1, $result);
		$except = "php+bi am excute operator!\$a=php,\$b=";
		$this->assertEquals($tmp, $except);
	
	}
}