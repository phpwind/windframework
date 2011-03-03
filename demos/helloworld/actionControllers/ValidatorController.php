<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.web.WindController');
L::import('WIND:component.utility.WindUtility');
class ValidatorController extends WindController {
    public function validatorFormRule() {
        $rules = array();
        $rules[] = WindUtility::buildValidateRule('name', 'isLegalLength', 5, 'xxx', 'ErrorNameLength');
		$rules[] = WindUtility::buildValidateRule('email', 'isEmail', array(), null, 'ErrorEmail!');
		$rules[] = WindUtility::buildValidateRule('sex', 'isLegalLength', array(), '1', 'ErrorSexType');
		$rules[] = WindUtility::buildValidateRule('age', 'isInt', array(), '20', 'ErrorAge');
		return $rules;
    }
    
    public function run() {
		echo '<h1>success:</h1><br/><ul>';
		echo '<li>validator in class : ' . __CLASS__ , '</li><br/>';
		echo '<li>your name is : ' . $this->getInput('name'), '</li><br/>';
		echo '<li>your age is :' . $this->getInput('age'), '</li><br/>';
		echo '<li>your sex is :' . $this->getInput('sex'), '</li><br/>';
		echo '<li>your email is :' . $this->getInput('email'), '</li><br/>';
		echo '</ul>';
		exit();
	}
}