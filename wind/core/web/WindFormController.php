<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

Wind::import('WIND:core.base.WindAction');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFormController extends WindAction {

	protected $formClass = '';

	/* (non-PHPdoc)
	 * @see WindAction::resolvedActionMethod()
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		$action = $handlerAdapter->getAction();
		if ($action !== 'run') $action = $this->resolvedActionName($action);
		try {
			$method = new ReflectionMethod($this, $action);
		} catch (Exception $exception) {
			throw new WindActionException('The action method ' . $action . ' is protected or not exist.');
		}
		if ($action !== 'doAction' && !$method->isAbstract() && $method->isPublic())
			call_user_func_array(array($this, $action), array());
		else
			throw new WindException();
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
	 * 返回form的类
	 * @return the $formClass
	 */
	public function getFormClass() {
		return $this->formClass;
	}
}

?>