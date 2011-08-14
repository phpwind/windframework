<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRoute extends AbstractWindRoute {
	/**
	 * 属性名称
	 *
	 * @var array
	 */
	protected $params = array();
	/**
	 * 正则表达式
	 *
	 * @var string
	 */
	protected $pattern;
	/**
	 * 用于反响生成Url的表达式
	 *
	 * @var string
	 */
	protected $reverse;

	/* (non-PHPdoc)
	 * @see IWindRoute::match()
	 */
	public function match() {
		//TODO 
	}

	/* (non-PHPdoc)
	 * @see IWindRoute::build()
	 */
	public function build() {
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setParams($this->getConfig('params'));
		$this->setPattern($this->getConfig('pattern'));
		$this->setReverse($this->getConfig('reverse'));
	}

}
?>