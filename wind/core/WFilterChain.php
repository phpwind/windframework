<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

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
class WFilterChain extends WFilter {
	/**
	 * 保存过滤链---过滤器对象队列
	 * @var  array $filters
	 */
	private $filters = array();
	/**
	 * 保存过滤链配置信息---过滤器配置信息队列
	 * 元素保存类型为: 'filter' => array('path' => 'path', 'rule' => 'rule')
	 * @var  array $filterChain
	 */
	private $filterChain = array();
	
	/**
	 * 构造函数·
	 * 用来初始化配置信息，并且解析配置信息调用
	 * @param WSystemConfig $configObj   配置对象实例
	 * @param WRouter $router   解析后的路由实例
	 */
	public function __construct($configObj, $router = null) {
		$this->init($configObj, $router);
	}
	
	/**
	 * 解析路由配置信息
	 * @param WSystemConfig $configObj  配置信息对象
	 * @param WRouter $router    解析后的路由实例
	 */
	public function init($configObj, $router) {
		$filterConfig = $configObj->getFilterChainConfig();//获得过滤链信息--含有过滤规则
		$action = $router->getAction();
		foreach ((array) $filterConfig as $key => $value) {
			$path = $configObj->getFiltersConfig($key);
			//TODO 规则解析
			if ($path)
				$this->filterChain[$key] = array(
					'name' => $key,
					'path' => $path, 
					'rule' => $value
				);
		}
	}
	
	/**
	 * 向过滤链中添加一个过滤器
	 * 如果该过滤器不是 WFilter的实例对象（该过滤器的实现没有继承WFilter基类），则将抛出一个异常
	 * @access private
	 * @param WFilter $filter 添加的具体过滤器实例对象
	 */
	private function addFilter($filter) {
		if ($filter instanceof WFilter) 
			$this->filters[get_class($filter)] = $filter;
		else
			throw new WException('This is not a WFilter object!!!');
	}
	
	/**
	 * 获得过滤器实例
	 * @param string $filter  需要获取的过滤器名称
	 */
	public function getFilter($filter) {
		if (!isset($this->filters[$filter])) {
			W::import($this->filterChain[$filter]['path']);
			$this->addFilter(new $filter());
		}
		return $this->filters[$filter];
	}
	
	/**
	 * 过滤链的调用入口
	 * 首先调用的是过滤链中所有被加载的过滤器的前置操作，然后调用回调函数，最后调用过滤器的后置操作
	 * @param array $callBack array('className', 'action')
	 * @param WHttpRequest $httpRequest
	 */
	public function doFilter($callBack, $httpRequest) {
		$this->doPreProcessing($httpRequest);
		$callBack = $this->parseCallBack($callBack);
		call_user_func_array($callBack, array(
			$httpRequest
		));
		$this->doPostProcessing($httpRequest);
		$this->destory();
	}
	
	/**
	 * 执行过滤链中的预操作
	 */
	public function doPreProcessing($httpRequest) {
		foreach ($this->filterChain as $filter => $info) {
			$this->getFilter($filter)->doPreProcessing($httpRequest);
		}
	}
	
	/**
	 * 执行过滤链中的后置操作
	 */
	public function doPostProcessing($httpRequest) {
		$count = count($this->filterChain);
		for ($i = $count; $i > 0; $i--) {
			$filter = array_pop($this->filterChain);
			$this->getFilter($filter['name'])->doPostProcessing($httpRequest);
		}
	}
	
	/**
	 * 解析回调函数
	 * @param array $callBack
	 * @return array array('className', 'action')
	 */
	private function parseCallBack($callBack) {
		if (!is_array($callBack))
			return array(
				'FrontController', 
				'excute'
			);
		list($className, $action) = $callBack;
		if (!class_exists($className, true)) {
			echo $className . '不存在<br/>';
			//throw new WException($className . '不存在', 100);
		}
		if ($action && !is_callable(array(
			$className, 
			$action
		))) {
			echo $className . '中,不存在' . $action . '<br/>';
			//throw new WException($className . '中,不存在' . $action, 100);
		}
		return $callBack;
	}
	
	/**
	 * 清空过滤链及过滤器配置链内容
	 * @access private
	 */
	private function destory() {
		$this->filterChain = array();
		$this->filters = array();
	}

}