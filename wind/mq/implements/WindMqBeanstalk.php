<?php
Wind::import('WIND:mq.exception.WindMqException');
Wind::import('WIND:mq.IWindMq');
/**
 * 基于beanstalk的消息队列实现
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @version $Id$
 * @package mq
 * @subpackage implements
 */
class WindMqBeanstalk implements IWindMq {
	
	

	/* (non-PHPdoc)
	 * @see IWindMq::getMq()
	 */
	public function getMq() {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::push()
	 */
	public function push($key, $value) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::pop()
	 */
	public function pop($key) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::range()
	 */
	public function range($key, $start = 0, $offset = 1) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::length()
	 */
	public function length($key) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::delete()
	 */
	public function delete($key) {
		// TODO Auto-generated method stub
	

	}
}

?>