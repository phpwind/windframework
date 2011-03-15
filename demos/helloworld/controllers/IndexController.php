<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.web.WindController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class IndexController extends WindController {

	protected $formClass = 'controllers.actionForm.IndexRunForm';

	public function run() {
        //$this->setLayout('layout');
		$this->setOutput(array('var1' => 'hello world from IndexController.'));
		$this->setTemplate('helloworld');
		//$this->forwardAction('add', 'WINDAPP1:default.index', '', true);
	    //echo $this->getUrlHelper()->createUrl('add');
	    /*$this->forwardAction('add', '', '', true);*/
		L::import('DEFAULT:dao.WindApp1DaoFactory');
		$dao = WindApp1DaoFactory::getFactory()->getDao('DEFAULT:dao.windApp1UserDao');
		return $dao->findUserById('3');
	}

	public function doHeader() {
		$this->setOutput('aaaaa', 'a');
		$this->setTemplate('header');
	}

	public function doFooter() {
		$this->setTemplate('footer');
	}

	public function doDelete() {
		echo 'hahahahahaha';
	}

	public function doAdd() {
		echo 'hello i am add.';
		$this->forwardAction('delete', '', '', true);
		$this->setTemplate('read');
		$this->sendError('hello world');
	}

}