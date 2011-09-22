<?php
/**
 * 将cookie作为对象操作
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.http.cookie
 */
class WindCookieObject {

	/**
	 * cookie前缀
	 * 
	 * @var string cookie前缀
	 */
	public $prefix;

	/**
	 * Cookie 名称
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Cookie 值
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * Cookie 过期时间
	 *
	 * @var int
	 */
	protected $expires;

	/**
	 * Cookie 域
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * Cookie 路径
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * 是否安全套接字
	 *
	 * @var boolean
	 */
	protected $secure;

	/**
	 * 是否启用编码
	 *
	 * @var boolean
	 */
	protected $encode;

	/**
	 * 是否可通过客户端脚本访问。
	 * 
	 * @var string httponly
	 */
	protected $httponly;

	/**
	 * 构造函数
	 * 
	 * 根据传入的cookie数据初始化cookie数据
	 * 
	 * @param string $name cookie名称
	 * @param string $value cookie值,默认为null
	 * @param string|int $expires 过期时间,默认为null即会话cookie,随着会话结束将会销毁
	 * @param string $path cookie保存的路径,默认为null即采用默认
	 * @param string $domain cookie所属域,默认为null即不设置
	 * @param boolean $secure 是否安全连接,默认为false即不采用安全链接
	 * @param boolean $httponly 是否可通过客户端脚本访问,默认为false即客户端脚本可以访问cookie
	 * @param string $prefix cookie前缀,默认为null即没有前缀
	 * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
	 * @return void
	 */
	public function __construct($name, $value = null, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false, $prefix = null, $encode = false) {
		$this->name = (string) $name;
		$this->value = (string) $value;
		$this->domain = (string) $domain;
		$this->expires = (null === $expires ? null : (int) $expires);
		$this->path = ($path ? $path : '/');
		$this->secure = $secure;
		$this->httponly = $httponly;
		$this->prefix = (string) $prefix;
		$this->encode = $encode;
	}

	/**
	 * 获取cookie的名称
	 * 
	 * @return string 获得cookie的名字
	 */
	public function getName() {
		return $this->prefix ? $this->prefix . $this->name : $this->prefix;
	}

	/**
	 * 获取cookie值
	 * 
	 * @return string 获得cookie值
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * 获取cookie的域
	 * 
	 * @return string 获得cookie域
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * 获取cookie的路径
	 * 
	 * @return string 获得cookie保存路径
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * 获取cookie的过期时间
	 * 
	 * @return mixed 获得cookie的过期时间
	 */
	public function getExpirs() {
		return $this->expires;
	}

	/**
	 * 是否是安全套接字
	 *
	 * @return boolean 获取是否需要安全链接
	 */
	public function isSecure() {
		return $this->secure;
	}

	/**
	 * 验证cookie是否过期
	 * 
	 * @param int|null $now 比较时间
	 * @return boolean 如果已经过期则返回true,否则返回false
	 */
	public function isExpired($now = null) {
		return (is_int($this->expires) && $this->expires < ($now ? $now : time())) ? true : false;
	}

	/**
	 * 判断是否是session cookie
	 * 
	 * @return boolean 如果过期时间是null则为会话cookie返回true,否则返回false
	 */
	public function isSessionCookie() {
		return null === $this->expires;
	}

	/**
	 * 格式化输出形式
	 * 
	 * @return string cookie信息
	 */
	public function __toString() {
		return $this->name . '=' . ($this->encode ? base64_encode($this->value) : $this->value) . ';';
	}

	/**
	 * 将cookie信息解析成对象输出
	 *
	 * @param string $cookiestr cookie信息
	 * @param string $prefix cookie的前缀,默认为null即没有前缀
	 * @param boolean $encode 是否编码,默认为false即为不编码
	 * @return WindCookieObject 如果输如的cookie信息串不合法则返回null
	 */
	public static function getCookieFromString($cookiestr, $prefix = null, $encode = false) {
		$cookie = explode(';', $cookiestr);
		list($name, $value) = explode('=', array_shift($cookie));
		if (empty($name)) {
			return null;
		}
		$domain = $expires = $path = null;
		$httponly = $secure = false;
		foreach ($cookie as $_cookie) {
			list($key, $_value) = explode('=', $_cookie);
			switch ($key) {
				case 'domain':
					$domain = $_value;
					break;
				case 'path':
					$path = $_value;
					break;
				case 'expires':
					$expires = is_int($_value) ? $_value : strtotime($_value);
					break;
				case 'httponly':
					$httponly = (bool) $_value;
					break;
				case 'secure':
					$secure = (bool) $_value;
					break;
			}
		}
		return new self($name, $value, $expires, $path, $domain, $secure, $httponly, $prefix, $encode);
	}
}