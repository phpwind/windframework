<?php

L::import('WIND:core.base.WindAction');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFormController extends WindAction {

	protected $formClass = '';

	/**
	 * @return the $formClass
	 */
	public function getFormClass() {
		return $this->formClass;
	}
}

?>