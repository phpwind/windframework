<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.base.IWindAction');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindBaseAction implements IWindAction {
	protected $request;
	protected $response;
	protected $mav = null;
	protected $error = null;
	
	protected $input = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct($request, $response) {
		L::import('WIND:core.WindModelAndView');
		$this->mav = new WindModelAndView();
		$this->mav->setViewName($response->getDispatcher()->getController() . '_' . $response->getDispatcher()->getAction());
		$this->error = WindErrorMessage::getInstance();
		$this->request = $request;
		$this->response = $response;
		$this->getInputParams();
	}
	
	public function beforeAction() {}
	abstract public function run();
	public function afterAction() {}
	
	/**
	 * 请求另一个操作处理
	 * 
	 * @param string $actionHandle
	 * @param string $path
	 */
	public function setAction($actionHandle = '', $path = '') {
		$this->getMav()->setAction($actionHandle, $path);
	}
	
	/* 数据处理 */
	
	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 */
	public function setOutput($data, $key = '') {
		$this->response->setData($data, $key);
	}
	
	/**
	 * 获得输入数据
	 * @param string|array $input
	 */
	public function getInput($input, $type) {

	}
	
	/* 错误处理 */
	
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
	 * @param string $message
	 * @param string $key
	 */
	public function sendError($message = '', $key = '', $errorAction = '') {
		$this->addError($message, $key);
		$this->addErrorAction($errorAction);
		$this->error->sendError();
	}
	
	/* 模板处理 */
	
	/**
	 * 设置页面模板
	 * @param string $template
	 */
	public function setTemplate($template = '') {
		if ($template) $this->getMav()->setViewName($template);
	}
	
	/**
	 * 设置页面布局
	 * @param WindLayout $layout
	 */
	public function setLayout($layout = '') {
		if ($layout instanceof WindLayout) {
			$this->getMav()->setLayout($layout);
		}
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function getMav() {
		return $this->mav;
	}
	
	private function getInputParams() {
		$this->input = new stdClass();
		foreach ($this->request->getGet() as $key => $value) {
			$this->input->$key = $value;
		}
		foreach ($this->request->getPost() as $key => $value) {
			$this->input->$key = $value;
		}
	}
}