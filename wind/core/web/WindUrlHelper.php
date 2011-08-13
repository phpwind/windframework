<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUrlHelper extends WindModule {
    
	/**
	 * 构造返回Url地址
	 * 
	 * 将根据是否开启url重写来分别构造相对应的url
	 * 
	 * @param string $action 执行的操作
	 * @param string $controller 执行的controller
	 * @param array $params 附带的参数
	 * @return string
	 */
	public function createUrl($action, $controller = '', $params = array()) {
		$router = $this->getSystemFactory()->getInstance(COMPONENT_ROUTER);
		return $router->buildUrl($action, $controller, $params);
	}
}
?>