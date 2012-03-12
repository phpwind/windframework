<?php
Wind::import('WIND:mq.exception.WindMqException');
Wind::import('WIND:mq.IWindMq');
/**
 * 基于redis list 消息队列实现,依赖与phpredis扩展
 * 
 * redis消息队列，支持<code>
 * push  支持优先级
 * pop
 * delete 支持批量删除($key1,$key2,$key3,$key4)
 * length
 * range
 * </code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @version $Id$
 * @package mq
 * @subpackage implements
 */
class WindMqRedis extends WindModule implements IWindMq {
	
	/**
	 * @var Redis
	 */
	protected $mq = null;
	
	private $zCache = '_zList'; //队列优先级实现
	private $host = '127.0.0.1';
	private $port = '6379';
	private $auth;

	/**
	 * @param string $connectStr 链接字符串
	 */
	public function __construct($host = '127.0.0.1', $port = '6379', $auth = '') {
		$this->host = $host;
		$this->port = $port;
		$this->auth = $auth;
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($_host = $this->getConfig('host')) $this->host = $_host;
		if ($_port = $this->getConfig('port')) $this->port = $_port;
		if ($_auth = $this->getConfig('auth')) $this->auth = $_auth;
	}

	/**
	 * @see IWindMq::getMq()
	 * @return Redis
	 */
	public function getMq() {
		if ($this->mq === null && !($this->mq = $this->_getMq())) {
			if (!class_exists('redis')) throw new WindMqException(
				"[mq.implements.WindMqRedis.getMq] Class 'redis' not found");
			
			$this->mq = new Redis();
			if (!$this->mq->connect($this->host, $this->port)) throw new WindMqException(
				"[mq.implements.WindMqRedis.getMq] Can't connect to " . $this->host . ":" . $this->port . ". name or service not known");
			if ($this->auth) {
				if (!$this->mq->auth($this->auth)) throw new WindMqException(
					'[mq.implements.WindMqRedis.getMq] Permission denied ');
			}
		}
		return $this->mq;
	}

	/**
	 * 向消息队列中插入一条数据
	 * 
	 * 增加优先级支持，优先级队列为一个唯一(值不可重复)队列
	 * @see IWindMq::push()
	 * @param string $key
	 * @param string $value
	 * @param int $priority 优先级，默认为null(无优先级)
	 * @return boolean
	 */
	public function push($key, $value, $priority = null) {
		$_result = false;
		if ($priority !== null) {
			$priority = intval($priority);
			$_result = $this->getMq()->zAdd($key, $priority, $value);
			$_result && $this->getMq()->sAdd($this->zCache, $key);
		} else
			$_result = $this->getMq()->rPush($key, $value);
		return $_result;
	}

	/* (non-PHPdoc)
	 * @see IWindMq::pop()
	 */
	public function pop($key) {
		$_result = '';
		if ($this->getMq()->sIsMember($this->zCache, $key)) {
			$_result = $this->getMq()->zRange($key, 0, 1);
			if (empty($_result)) return '';
			$this->getMq()->zDelete($key, $_result[0]);
			$_result = $_result[0];
		}
		$_result = $this->getMq()->lPop($key);
		return $_result;
	}

	/* (non-PHPdoc)
	 * @see IWindMq::range()
	 */
	public function range($key, $start = 0, $offset = 1) {
		$start = intval($start);
		$offset = intval($offset);
		$_r = $this->getMq()->sIsMember($this->zCache, $key);
		return call_user_func_array(array($this->getMq(), ($_r ? 'zRange' : 'lRange')), 
			array($key, $start, $start + ($offset - 1)));
	}

	/* (non-PHPdoc)
	 * @see IWindMq::length()
	 */
	public function length($key) {
		$_r = $this->getMq()->sIsMember($this->zCache, $key);
		return $key ? call_user_func_array(array($this->getMq(), ($_r ? 'zSize' : 'lSize')), 
			array($key)) : 0;
	}

	/* (non-PHPdoc)
	 * @see IWindMq::clear()
	 */
	public function delete($key) {
		$this->getMq()->sRem($this->zCache, $key);
		return call_user_func_array(array($this->getMq(), 'delete'), func_get_args());
	}

}

?>