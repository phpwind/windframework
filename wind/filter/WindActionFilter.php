<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class WindActionFilter extends WindHandlerInterceptor {
	protected $_vars = array();
	/**
	 * @var WindForward
	 */
	protected $forward = null;
	/**
	 * @var WindErrorMessage
	 */
	protected $errorMessage = null;

	/**
	 * @param WindForward $forward
	 * @param WindErrorMessage $errorMessage
	 */
	public function __construct($forward, $errorMessage) {
		$this->forward = $forward;
		$this->errorMessage = $errorMessage;
		$args = func_get_args();
		unset($args[0], $args[1]);
		foreach ($args as $key => $value)
			property_exists(get_class($this), $key) && $this->$key = $value;
		$this->_vars = $forward->getVars();
	}

	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setOutput($data, $key = '') {
		$this->forward->setVars($data, $key);
	}

	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setGlobal($data, $key = '') {
		$this->forward->setVars($data, $key, true);
	}

	/**
	 * 获得输入数据
	 * 如果输入了回调方法则返回数组:
	 * 第一个值：value
	 * 第二个值：验证结果
	 * @param string $name input name
	 * @param string $type input type (GET POST COOKIE)
	 * @param string $callback | validation for input
	 * @return array | string
	 */
	protected function getInput($name, $type = '', $callback = array()) {
		$value = '';
		switch (strtolower($type)) {
			case 'form':
				$value = $this->getRequest()->getData($name);
				break;
			case IWindRequest::INPUT_TYPE_GET:
				$value = $this->getRequest()->getGet($name);
				break;
			case IWindRequest::INPUT_TYPE_POST:
				$value = $this->getRequest()->getPost($name);
				break;
			case IWindRequest::INPUT_TYPE_COOKIE:
				$value = $this->getRequest()->getCookie($name);
				break;
			default:
				$value = $this->getRequest()->getAttribute($name);
		}
		return $callback ? array($value, call_user_func_array($callback, array($value))) : $value;
	}

}

?>