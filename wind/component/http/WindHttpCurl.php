<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.http.base.WindHttp');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindHttpCurl extends WindHttp{
	
	private function __construct($url = ''){
		parent::__construct($url);
	}
	
	public function open(){
		if(null === $this->httpResource){
			$this->httpResource = curl_init();
		}
		return $this->httpResource;
	}
	
	public function setParam($name,$value){
		return curl_setopt($this->httpResource,$name,$value);
	}
	
	public function setParamsByArray($opt = array()){
		return curl_setopt_array($this->httpResource,$opt);
	}
	
	public function request(){
		return curl_exec($this->httpResource);
	}
	
	
	public function close(){
		if($this->httpResource){
			curl_close($this->httpResource);
			$this->httpResource = null;
		}
	}
	
	public function getError(){
		$err = curl_error($this->httpResource);
		$eno = curl_errno($this->httpResource);
		return $err ? array($err,$eno) : array();
	}
	
	public  function post($url = '',$data = array(),$timeout = 3,$header=array(),$cookie = array(),$options = array()){
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::POST, $timeout,$options);
	}
	public  function get($url = '',$data = array(),$timeout = 3,$header=array(),$cookie = array(),$options = array()){
		$url && $this->setUrl($url);
		$header && is_array($header) && $this->setHeaders($header);
		$cookie && is_array($cookie) && $this->setCookies($cookie);
		$data && is_array($data) && $this->setDatas($data);
		return $this->send(self::GET, $timeout,$options);
	}
	public  function send($method = self::GET,$timeout = 3,$options = array()){
		if(null === $this->httpResource){
			$this->open();
		}
		$this->setParams(CURLOPT_HEADER,0);
		$this->setParams(CURLOPT_FOLLOWLOCATION,1);
		$this->setParams(CURLOPT_RETURNTRANSFER,1);
		$this->setParams(CURLOPT_TIMEOUT,$timeout);   
		if ($options && is_array($options)) {
			$this->setParamsByArray($options);
		}
		if (self::GET === $method && !empty($this->data)) {
			$get = self::buildQuery($this->data,'&');
			$url = parse_url($this->url);
			$sep = isset($url['query']) ? '&' : '?';
			$this->url .= $sep . $get;
		}
		if (self::POST === $method) {
			$this->setParams(CURLOPT_POST,1);
			$this->setParams(CURLOPT_POSTFIELDS,self::buildQuery($this->cookie,'&'));
		}
		if ($this->cookie && is_array($this->cookie)) {
			$this->setParams(CURLOPT_COOKIE,self::buildQuery($this->cookie,';'));  
		}
		if (empty($this->header)) {
			$this->setHeader('User-Agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1');
		}
		$this->setParams(CURLOPT_HTTPHEADER, self::buildArray($this->header,':'));
		$this->setParams(CURLOPT_URL,$this->url); 
		return $this->request();
	}
	
	public function __construct(){
		$this->close();
	}
	
}