<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

/**
 * 过滤器类抽象类
 *
 * 用户要添加自己的过滤器类，则必须继承该抽象类,并且用户通过实现：
 * doPreProcessing和doPostProcessing来加入过滤链中。
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$
 * @package
 */
abstract class WindFilter {
	/**
	 * 保存该过滤器的配置信息
	 * @var mixed $filterName
	 */
	protected $filterConfig = '';
	/**
	 * 初始化过滤器，设置该过滤器的配置信息
	 * @param WSystemConfig $configObj
	 */
	public function init($configObj = null) {}
	
	/**
	 * @param WRequest $request
	 * @param WResponse $response
	 */
	public function doFilter($request, $response) {
		$this->doBeforeProcess($request, $response);
		$filter = WFilterFactory::getFactory()->create();
		if ($filter != null) {
			if (!in_array(__CLASS__, class_parents($filter))) throw new WException(get_class($filter) . ' is not extend a filter class!');
			
			$filter->doFilter($request, $response);
		} else
			WFilterFactory::getFactory()->execute();
		$this->doAfterProcess($request, $response);
	}
	
	/**
	 * 获得过滤器配置信息
	 * @return mixed
	 */
	public function getFilterConfig() {
		return $this->filterConfig;
	}
	/**
	 * 用户需要实现
	 * 预操作，由用户的实现来决定用户的该操作是预操作还是后置操作
	 * @param WHttpRequest $httpRequest
	 */
	abstract protected function doBeforeProcess($request, $response);
	
	/**
	 * 用户需要实现
	 * 后置操作
	 * @param WHttpRequest $httpRequest
	 */
	abstract protected function doAfterProcess($request, $response);
}