<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
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
class IndexController extends WindController {

	public function run() {
		$this->setOutput(array('var1' => 'hello world from IndexController.'));
		$this->setTemplate('helloworld');
		L::import('WIND:core.dao.WindDaoFactory');
		$dao = WindDaoFactory::getFactory()->instantiateDao('WINDAPP1:dao.windApp1UserDao');
		$dao->findUserById('1');
	}

	public function redirect() {
		$this->forwardRedirect('http://www.baidu.com');
	}

	public function add() {
		echo 'hello i am add.';
		$this->setTemplate('read');
	}

}