<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('filter.WFilterChain');
/**
 * 实现filter的管理类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WFilterManager {
	/**
	 * 保存过滤器链的对象
	 * @var WFilterChain $filterChain
	 */
	private $filterChain;
	/**
	 * 保存过滤器的配置信息
	 * @var array
	 */
	private $filterConfig;
	/**
	 * 构造过滤器的管理类
	 * @param WConfigParser $configParser
	 */
	public function __construct($configParser) {
		$this->filterChain = new WFilterChain();
		$this->filterConfig = $configParser->filterConfig;
		$this->addFilters();
	}
	
	/**
	 * 根据配置文件装载过滤器
	 * @param WConfigParser $configParser
	 */
	private function addFilters() {
		foreach ($this->filterConfig as $filter) {
			if (!class_exists($filter, true) ) {
				echo ('过滤器' . $filter . '不存在<br/>');
			  // throw new Exception('过滤器' . $filter . '不存在');
			   continue;
			}
			$this->filterChain->addFilter(new $filter());
		}
	}
	
	/**
	 * 控制器转发入口
	 * @param array $callBack  回调函数
	 * @param WHttpRequest $httpRequest
	 */
	public function filterProcessing($callBack, $httpRequest) {
		$this->filterChain->doFilter($callBack, $httpRequest);
	}
}

