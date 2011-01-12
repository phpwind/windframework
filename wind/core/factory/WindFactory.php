<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.factory.AbstractWindFactory');
/**
 * Wind容器基类，创建类对象（分为两种模式，一种是普通模式，一种为单利模式）
 * 
 * 职责：
 * 类创建
 * 统一类接口访问
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFactory extends AbstractWindFactory {
	const CLASSES_DEFINITIONS = 'classes';
	
	private $classProxy = null;
	
	/**
	 * @return the $classProxy
	 */
	public function getClassProxy() {
		return $this->classProxy;
	}
	
	/**
	 * @param WindClassProxy $classProxy
	 */
	public function setClassProxy($classProxy) {
		$this->classProxy = $classProxy;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFactory::createInstance()
	 */
	public function createInstance($className, $args) {
		if (!class_exists($className)) throw new WindException('create class instance error. class ' . $className . 'is not exists.');
		$reflection = new ReflectionClass($className);
		if ($reflection->isAbstract() || $reflection->isInterface()) return;
		
		$object = call_user_func_array(array($reflection, 'newInstance'), (array) $args);
		return $object;
	}
	

}