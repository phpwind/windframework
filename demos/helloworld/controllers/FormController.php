<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.web.WindFormController');

/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$ 2011-3-3
 * @package
 */
class FormController extends WindFormController {
    protected $formClass = 'actionControllers.actionForm.MemberForm';
	
    public function run() {
		$formData = $this->getInput('formData');
		echo '<h1>success:</h1><br/><ul>';
		echo '<li>get the formData of : ' . get_class($formData) , '</li><br/>';
		echo '<li>your name is : ' . $formData->getName(), '</li><br/>';
		echo '<li>your age is :' . $formData->getAge(), '</li><br/>';
		echo '<li>your sex is :' . $formData->getSex(), '</li><br/>';
		echo '<li>your email is :' . $formData->getEmail(), '</li><br/>';
		echo '</ul>';
		exit();
	}
}