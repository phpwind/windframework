<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * WindMemcache操作
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMemcache {
	
	const HOST = 'host';
	const PORT = 'port';
	const PCONN = 'pconn';
	const WEIGHT = 'weight';
	const TIMEOUT = 'timeout';
	const RETRY = 'retry';
	const STATUS = 'status';
	const FCALLBACK = 'fcallback';
	const COMPRESS = 'compress';
	const SERVERCONFIG = 'servers';
	
	/**
	 * @var Memcache
	 */
	private $memcache = null;
	
	public function __construct() {
		if (!extension_loaded('Memcache')) {
			throw new WindException('', 'The memcache extension must be loaded !');
		}
		$this->memcache = new Memcache();
	}
	
	/**
	 * 设置一个指定 key 的缓存变量内容
	 * @param string $key 缓存数据的键， 其长度不能超过250个字符
	 * @param mixed $value 值，整型将直接存储，其他类型将被序列化存储，其值最大为1M
	 * @param int $flag 是否使用 zlib 压缩
	 * @param int $expire 过期时间，0 为永不过期，可使用 unix 时间戳格式或距离当前时间的秒数，设为秒数时不能大于 2592000（30 天）
	 */
	public function set($key,$value,$flag = 0,$expire = 0){
		return $this->memcache->set($key,$value,$flag,$expire);
	}
	
	/**
	 * 获取某个或者一组 key 的变量缓存值
	 * @param mixed $key
	 */
	public function get($key){
		return $this->memcache->get($key);
	}
	
	
	/**
	 * 删除某一个或一组变量的缓存
	 * @param string $key 存的键 键值不能为null和'’，当它等于前面两个值的时候php会有警告错误。
	 * @param int $timeout 删除这项的时间，如果它等于0，这项将被立刻删除反之如果它等于30秒，那么这项被删除在30秒内
	 */
	public function delete($key,$timeout = 0){
		return $this->memcache->delete($key,$timeout);
	}
	
	/**
	 * 清空所有缓存内容，不是真的删除缓存的内容，只是使所有变量的缓存过期，使内存中的内容被重写
	 */
	public function flush(){
		$this->memcache->flush();
	}
	/**
	 * 取得缓存操作句柄
	 * @return Memcache
	 */
	public function getMemcache(){
		return $this->memcache;
	}
	/**
	 * 批量添加memecache服务器
	 * @param array $servers
	 */
	public function setServers(array $servers) {
		foreach ($servers as $server) {
			if (!is_array($server)) {
				throw new WindException('The memcache config is incorrect');
			}
			$this->setServer($server);
		}
	}
	
	/**
	 * 添加memached服务器
	 * @example  $server = array(
	 * array(
	 * 'host'=>'localhost',
	 * 'port'=>11211
	 * 'pconn'=>true
	 * ),
	 * array(
	 * 'host'=>'localhost',
	 * 'port'=>11212
	 * 'pconn'=>false
	 * )
	 * @param array $server
	 */
	public function setServer(array $server) {
		if (!isset($server[self::HOST])) {
			throw new WindException('The memcache server ip address is not exist');
		}
		if (!isset($server[self::PORT])) {
			throw new WindException('The memcache server port is not exist');
		}
		$_server = array();
		$_server[self::HOST] = $server[self::HOST];
		$_server[self::PORT] = $server[self::PORT];
		$_server[self::PCONN] = isset($server[self::PCONN]) ? $server[self::PCONN] : true;
		$_server[self::WEIGHT] = isset($server[self::WEIGHT]) ? $server[self::WEIGHT] : 1;
		$_server[self::TIMEOUT] = isset($server[self::TIMEOUT]) ? $server[self::TIMEOUT] : 15;
		$_server[self::RETRY] = isset($server[self::RETRY]) ? $server[self::RETRY] : 15;
		$_server[self::STATUS] = isset($server[self::STATUS]) ? $server[self::STATUS] : true;
		$_server[self::FCALLBACK] = isset($server[self::FCALLBACK]) ? $server[self::FCALLBACK] : null;
		list($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback) = array_values($_server);
		$this->memcache->addServer($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback);
	}
	
	
	public function close(){
		return $this->memcache->close();
	}
	
	public function __destruct(){
		$this->close();
	}
}