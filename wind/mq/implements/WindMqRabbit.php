<?php
Wind::import('WIND:mq.exception.WindMqException');
Wind::import('WIND:mq.IWindMq');
/**
 * Enter description here ...
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @version $Id$
 * @package 
 */
class WindMqRabbit implements IWindMq {

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
	 * @see IWindMq::pushR()
	 */
	public function pushR($key, $value) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::PopL()
	 */
	public function PopL($key) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::set()
	 */
	public function set($key, $index, $value) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::get()
	 */
	public function get($key, $index) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::batchPush()
	 */
	public function batchPush($keys, $values) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::range()
	 */
	public function range($key, $start = 0, $offset = 1) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::increment()
	 */
	public function increment($key, $by = null) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::decrement()
	 */
	public function decrement($key, $by = null) {
		// TODO Auto-generated method stub
	

	}

	/* (non-PHPdoc)
	 * @see IWindMq::clear()
	 */
	public function clear($key) {
		// TODO Auto-generated method stub
	

	}

}

?>