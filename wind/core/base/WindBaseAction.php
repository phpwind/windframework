<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindBaseAction {
	private $request;
	private $response;
	/**
	 * 页面跳转信息model and view对象
	 * 
	 * @var $mav WindModelAndView
	 */
	protected $mav = null;
	protected $error = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct($request, $response) {
		L::import('WIND:core.WindModelAndView');
		$this->mav = new WindModelAndView();
		$this->request = $request;
		$this->response = $response;
		$this->setDefaultViewTemplate();
		$this->error = WindErrorMessage::getInstance();
	}
	
	public function beforeAction() {}
	abstract public function run();
	public function afterAction() {}
	
	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 */
	public function setViewData($data, $key = '') {
		$this->response->setData($data, $key);
	}
	
	/**
	 * 设置默认模板
	 */
	public function setDefaultViewTemplate($default = '') {
		if (!$default && $this->response) {
			$default = $this->response->getDispatcher()->getController() . '_' . $this->response->getDispatcher()->getAction();
		}
		$this->mav->setViewName($default);
	}
	
	/**
	 * 设置页面模板
	 * @param string $template
	 */
	public function setTemplate($template = '') {
		if ($template) $this->getModelAndView()->setViewName($template);
	}
	
	/**
	 * 设置页面布局
	 * @param WindLayout $layout
	 */
	public function setLayout($layout = '') {
		if ($layout instanceof WindLayout) {
			$this->getModelAndView()->setLayout($layout);
		}
	}
	
	/**
	 * 添加错误信息
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '') {
		$this->error->addError($message, $key);
	}
	
	/**
	 * 添加错误处理Action
	 */
	public function addErrorAction($errorAction) {
		$this->error->setErrorAction($errorAction);
	}
	
	/**
	 * @param string $message
	 * @param string $key
	 */
	public function sendError($message = '', $key = '', $errorAction = '') {
		$this->addError($message, $key);
		$this->addErrorAction($errorAction);
		$this->error->sendError();
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function getModelAndView() {
		return $this->mav;
	}

}