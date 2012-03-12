<?php
Wind::import('WIND:mq.exception.WindMqException');
Wind::import('WIND:mq.IWindMq');
/**
 * 基于memcacheq的队列实现
 * 
 * memcacheq只支持<code>
 * push
 * pop
 * delete</code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @version $Id$
 * @package mq
 * @subpackage implements
 */
class WindMqMemcacheq extends WindModule implements IWindMq {
	
	/**
	 * @var Memcahce
	 */
	protected $mq = null;
	
	private $host = '127.0.0.1';
	private $port = '21201';

	/**
	 * @param string $connectStr 链接字符串
	 */
	public function __construct($host = '127.0.0.1', $port = '21201') {
		$this->host = $host;
		$this->port = $port;
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($_host = $this->getConfig('host')) $this->host = $_host;
		if ($_port = $this->getConfig('port')) $this->port = $_port;
	}

	/* (non-PHPdoc)
	 * @see IWindMq::getMq()
	 */
	public function getMq() {
		if ($this->mq === null && !($this->mq = $this->_getMq())) {
			if (!class_exists('memcache')) throw new WindMqException(
				"[mq.implements.WindMqRedis.getMq] Class 'redis' not found");
			$this->mq = new Memcache();
			if (!$this->mq->connect($this->host, $this->port)) throw new WindMqException(
				"[mq.implements.WindMqMemcacheq.getMq] Can't connect to " . $this->host . ":" . $this->port . ". name or service not known");
		}
		return $this->mq;
	}

	/* (non-PHPdoc)
	 * @see IWindMq::push()
	 */
	public function push($key, $value) {
		return $this->getMq()->set($key, $value);
	}

	/* (non-PHPdoc)
	 * @see IWindMq::pop()
	 */
	public function pop($key) {
		return $this->getMq()->get($key);
	}

	/* (non-PHPdoc)
	 * @see IWindMq::range()
	 */
	public function range($key, $start = 0, $offset = 1) {
		throw new WindMqException("[mq.WindMqMemcacheq.range] unimplemented method 'range'");
	}

	/* (non-PHPdoc)
	 * @see IWindMq::length()
	 */
	public function length($key) {
		throw new WindMqException("[mq.WindMqMemcacheq.range] unimplemented method 'range'");
	}

	/* (non-PHPdoc)
	 * @see IWindMq::delete()
	 */
	public function delete($key) {
		foreach (func_get_args() as $_key) {
			if ($this->getMq()->delete($_key)) continue;
			return false;
		}
		return true;
	}

	public function __destruct() {
		$this->getMq()->close();
	}

}

?>