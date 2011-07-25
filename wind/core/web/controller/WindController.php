<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindController extends WindSimpleController {
	/**
	 * 验证类
	 *
	 * @var string
	 */
	protected $validatorClass = 'WIND:component.utility.WindValidator';
	/**
	 * 表单类
	 *
	 * @var string
	 */
	protected $formClass = '';

	/* (non-PHPdoc)
	 * @see WindSimpleController::run()
	 */
	public function run() {}

	/* (non-PHPdoc)
	 * @see WindSimpleController::preAction()
	 */
	final public function preAction($handlerAdapter) {
		parent::preAction($handlerAdapter);
		if ($formClassPath = $this->getFormClass()) {
			$this->registerEventListener('doAction', 
				new WindFormListener($this->request, $formClassPath, $this->getErrorMessage()));
		} elseif ($rules = $this->validatorFormRule($handlerAdapter->getAction())) {
			if (!isset($rules['errorMessage'])) {
				$rules['errorMessage'] = $this->getErrorMessage();
			}
			$this->registerEventListener('doAction', 
				new WindValidateListener($this->request, $rules, $this->getValidatorClass()));
		}
	}

	/* (non-PHPdoc)
	 * @see WindAction::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$_temp = $handlerAdapter->getController() . '_' . $handlerAdapter->getAction();
		$this->setTemplate($_temp);
	}

	/* (non-PHPdoc)
	 * @see WindAction::resolvedActionMethod()
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		$action = $handlerAdapter->getAction();
		if ($action !== 'run')
			$action = $this->resolvedActionName($action);
		try {
			if ($action == 'doAction')
				throw new WindException('[core.web.WindController.resolvedActionMethod]', 
					WindException::ERROR_CLASS_METHOD_NOT_EXIST);
			$method = new ReflectionMethod($this, $action);
			if ($method->isAbstract() || !$method->isPublic())
				throw new WindException('[core.web.WindController.resolvedActionMethod]', 
					WindException::ERROR_CLASS_METHOD_NOT_EXIST);
			return $action;
		} catch (Exception $exception) {
			throw new WindException(
				'[core.web.WindController.resolvedActionMethod] action method:' . $action . ' exception message:' .
					 $exception->getMessage());
		}
	}

	/**
	 * 根据请求的Action值返回Action的真正方法名
	 * 可以通过覆盖该方法来改变Action的命名规则
	 * @param string $action
	 * @return string
	 */
	protected function resolvedActionName($action) {
		return $action . 'Action';
	}

	/**
	 * 实现表单验证规则
	 * @param string $type
	 */
	protected function validatorFormRule($type) {
		return array();
	}

	/**
	 * 返回form的类
	 * @return string
	 */
	protected function getFormClass() {
		return $this->formClass;
	}

	/**
	 * 返回数据验证类
	 * @return string
	 */
	protected function getValidatorClass() {
		return $this->validatorClass;
	}
}