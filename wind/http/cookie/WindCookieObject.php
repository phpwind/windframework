<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 将cookie作为对象操作
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindCookieObject{
	
	/**
	 * @var string cookie前缀
	 */
	public  $prefix;
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
     * @var string httponly
     */
    protected $httponly;
    
 	 /**
     * @param string $name
     * @param string $value
     * @param string $domain
     * @param int $expires
     * @param string $path
     * @param bool $secure
     * @param bool $httponly
     * @param int $prefix
     * @param bool $encode
     */
    public function __construct($name, $value=null, $expires = null, $path = null,$domain =null, $secure = false,$httponly=false,$prefix=null,$encode = false){
       
        $this->name = (string) $name;
        $this->value = (string) $value;
        $this->domain = (string) $domain;
        $this->expires = (null === $expires ? null : (int) $expires);
        $this->path = ($path ? $path : '/');
        $this->secure = $secure;
        $this->httponly = $httponly;
        $this->prefix = (string)$prefix;
        $this->encode = $encode;
    }
	/**
     * 获取cookie的名称
     * @return string
     */
    public function getName(){
        return $this->prefix ? $this->prefix.$this->name : $this->prefix;
    }
    /**
     *获取cookie值
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * 获取cookie的域
     * @return string
     */
    public function getDomain(){
        return $this->domain;
    }
    /**
     *  获取cookie的路径
     * @return string
     */
    public function getPath(){
        return $this->path;
    }
    /**
     *获取cookie的过期时间
     * @return int|null
     */
    public function getExpirs(){
        return $this->expires;
    }
    /**
     *是否是安全套接字
     * @return boolean
     */
    public function isSecure(){
        return $this->secure;
    }
    /**
     * 验证cookie是否过期
     * @param int|null $now 比较时间
     * @return boolean
     */
    public function isExpired($now = null){
    	return (is_int($this->expires) && $this->expires < ($now ? $now : time())) ? true : false;
    }

    /**
     *是否是session cookie
     * @return boolean
     */
    public function isSessionCookie(){
        return null === $this->expires;
    }
    
	/**
	 * @return string
	 */
	public function __toString(){
		return  $this->name . '='. ($this->encode ? urlencode($this->value) : $this->value) .';';
    }
    
    public static function getCookieFromString($cookiestr,$prefix = null,$encode = false){
    	$cookie = explode(';',$cookiestr);
    	list($name,$value) = explode('=',array_shift($cookie));
    	if(empty($name)){
    		return null;
    	}
    	$domain=$expires =$path = null;
    	$httponly = $secure = false;
    	foreach($cookie as $_cookie){
    		list($key,$_value) = explode('=',$_cookie);
    		switch($key){
    			case 'domain':$domain=$_value;break;
    			case 'path':$path=$_value;break;
    			case 'expires':$expires = is_int($_value) ? $_value : strtotime($_value);break;
    			case 'httponly':$httponly=(bool)$_value;break;
    			case 'secure':$secure=(bool)$_value;break;
    		}
    	}
    	return new self($name,$value,$expires,$path,$domain,$secure,$httponly,$prefix,$encode);
    }
}