<?php
/**
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFormListener extends WindHandlerInterceptor {
	
	/**
	 * @var WindHttpRequest
	 */
	private $request = null;
	
	private $formPath = '';
	
	private $errorMessage = null;

	/**
	 * @param WindHttpRequest $request
	 * @param string $formPath
	 */
	public function __construct($request, $formPath, $errorMessage) {
		$this->request = $request;
		$this->formPath = $formPath;
		$this->errorMessage = $errorMessage;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$className = Wind::import($this->formPath);
		if (!class_exists($className))
			throw new WindException('the form \'' . $this->formPath . '\' is not exists!');
		if ('WindEnableValidateModule' != get_parent_class($className))
			throw new WindException('the form \'' . $this->formPath . '\' is not extends \'WindEnableValidateModule\'!');
		$form = new $className();
		$methods = get_class_methods($form);
		foreach ($methods as $method) {
			if ((0 !== strpos($method, 'set')) || ('' == ($_tmp = substr($method, 3))))
				continue;
			$_tmp[0] = strtolower($_tmp[0]);
			$value = $this->request->getPost($_tmp) ? $this->request->getPost($_tmp) : $this->request->getGet($_tmp);
			if (null === $value)
				continue;
			call_user_func_array(array($form, $method), array($value));
		}
		call_user_func_array(array($form, 'validate'), array($form));
		if (($error = $form->getErrors())) {
			list($errorController, $errorAction) = $form->getErrorControllerAndAction();
			$this->sendError($errorController, $errorAction, $error);
		}
		$this->request->setAttribute('formData', $form);
	}

	private function sendError($errorController, $errorAction, $errors) {
		$this->errorMessage->setErrorController($errorController);
		$this->errorMessage->setErrorAction($errorAction);
		$this->errorMessage->addError($errors);
		$this->errorMessage->sendError();
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		// TODO Auto-generated method stub
	}
}

?>