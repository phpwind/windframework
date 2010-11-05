<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('filter.WFilter');
/**
 * 过滤链的实现
 * 
 * 过滤链的实现，通过addFilter动态往链中添加过滤器，doFilter是该过滤链的入口地址，doPreProcessing则执行过滤链
 * 中的所有前置操作，doPostProcessing则执行过滤链中所有后置操作。
 * 在doFilter中需要用户设置回调函数$callBack，该回调函数的设置形式如下：
 * array('$controller', '$action')的形式，
 * $controller是该$action所在的控制器对象，
 * $action 是具体用户需要的操作。
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WFilterChain extends WFilter{
	/**
	 * 保存过滤链
	 * @var array
	 */
	private $filterChain = array();
	
	/**
	 * 保存过滤链中过滤器的个数
	 * @var integer
	 */
	private $count = 0;
	
	/**
	 * 构造器
	 */
	public function __construct() {
		$this->filterChain =  array();
		$this->count = 0;
	}
	/**
	 * 向过滤链中添加一个过滤器
	 * @param WFilter $filter
	 */
	public function addFilter($filter) {
		if ($filter instanceof WFilter) {
			$this->filterChain[] = $filter;
			$this->count ++;
		} else {
			echo $filter . '不是有效的过滤器!请检查是否继承了基类Filter!<br/>';
		}
	}
	
	/**
	 * 过滤链的调用入口
	 * @param array $callBack | array('className', 'action')
	 * @param WHttpRequest $httpRequest
	 */
	public function doFilter($callBack, $httpRequest) {
		$this->doPreProcessing($httpRequest);
		$callBack = $this->parseCallBack($callBack);
		call_user_func_array($callBack, array($httpRequest));
		$this->doPostProcessing($httpRequest);
	}
	
	/**
	 * 执行过滤链中的预操作
	 */
	public function doPreProcessing($httpRequest) {
		foreach ($this->filterChain as $filter) {
			$filter->doPreProcessing($httpRequest);	
		}
	}
	
	/**
	 * 执行过滤链中的后置操作
	 */
	public function doPostProcessing($httpRequest) {
		for ($i = 0; $i < $this->count; $i++) {
			$filter = array_pop($this->filterChain);
			$filter->doPostProcessing($httpRequest);
		}
	}
	
	/**
	 * 解析回调函数
	 * @param array $callBack
	 * @return array
	 */
	private function parseCallBack($callBack) {
		if (!is_array($callBack)) return array('FrontController', 'excute');
		list($className, $action) = $callBack;
		if (!class_exists($className, true)) {
			echo $className . '不存在<br/>';
			//throw new ClassNoExitsException($className . '不存在', 100);
			//return false;
		}
		if ($action && !is_callable(array($className,	$action))) {
			echo $className . '中,不存在' . $action .'<br/>';
			//throw new NoOperatorExitsException($className . '中,不存在' . $action, 100);
			//return false;
		}
		return $callBack;
	}
	
	public function  __destruct(){
		$this->filterChain = NULL;
		$this->count = NULL;
		unset($this->filterChain);
		unset($this->count);
	}
}