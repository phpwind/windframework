<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindController extends WindSimpleController {

	/* (non-PHPdoc)
	 * @see WindAction::resolvedActionMethod()
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		$action = $handlerAdapter->getAction();
		if ($action !== 'run')
			$action = $this->resolvedActionName($action);
		if ($action == 'doAction')
			throw new WindException('[core.web.WindController.resolvedActionMethod]', 
				WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		$method = new ReflectionMethod($this, $action);
		if ($method->isAbstract() || !$method->isPublic()) {
			throw new WindException('[core.web.WindController.resolvedActionMethod]', 
				WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
		return $action;
	}

	/**
	 * @param string $action
	 * @throws WindException
	 */
	protected function resolvedActionName($action) {
		return $action . 'Action';
	}
}