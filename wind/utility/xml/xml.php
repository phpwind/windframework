<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class XML {
	protected $XMLData;
	protected $object;
	protected $outputEncoding;
	
	public function __construct($data, $encoding) {
		$this->setXMLData($data);
		$this->setOutputEncoding($encoding);
	}
	
	public function setXMLData($data) {
		if (!$data) return false;
		if ($this->isXMLFile($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('输入参数不是有效的xml格式');
		}
	}
	public function setOutputEncoding($encoding) {
		if ($encoding) $this->outputEncoding = strtoupper(trim($encoding));
	}
	
	/**
	 * 根据指定文件路径读取XML数据
	 *
	 * @param string $filePath
	 */
	public function setXMLFile($filePath) {
		$filePath = realpath($filePath);
		if (!is_file($filePath) || strtolower(substr($filePath, -4)) != '.xml') throw new Exception("你输入的xml文件不是有效的xml文件");
		$this->setXMLData(file_get_contents($filePath));
	}
	
	/**
	 * 是否为xml格式文件
	 *
	 * @return unknown
	 */
	private function isXMLFile($data) {
		if (strpos(strtolower($data), '<?xml') === false) {
			return false;
		}
		return true;
	}
	
	/**
	 * 根据指定URL读取XML数据
	 *
	 * @param string $url
	 */
	public function setXMLUrl($url) {
		$this->setXMLData(XML::PostHost($url));
	}
	
	public function doParser() {
   		$this->object = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	public function getXMLDocument() {
		return $this->object;
	}
	
	public function getElementByXPath($tagPath) {
		if ($tagPath) {
			return $this->object->xpath($tagPath);
		}
	}
    public function getContentsList($elements) {
    	(!is_array($elements)) && $elements = array($elements);
    	$_result = array();
    	foreach ($elements as $key => $element) {
    		$_result[] = self::getTagContents($element);
    	}
    	return $_result;
    }
    
	public function getTagContents($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = strval($element[0]);
		$_array['attributes'] = self::getAttributes($element);
		$_array['children'] = self::getChilds($element);
		return $_array;
	}
	
	/**
	 * 返回节点的属性
	 * 使用XML::getAttributes($element);
	 * @param SimpleXMLElement $element
	 * @return array  返回该节点的属性
	 */
	public function getAttributes($element) {
		$_attributes = array();
		$attributes = $element->attributes();
		if (!$attributes) return $_attributes;
		
		foreach ($attributes as $key => $value) {
			$_attributes[$key] = strval($value);
		}
		return $_attributes;
	}
	
	public function getChilds($element) {
		$_childs = array();
		$childs = $element->children();
		if (!$childs) return $_childs;
		foreach ($childs as $key => $value) {
			$_childs[] = self::getTagContents($value);
		}
		return $_childs;
	}
	
	public function dataConvert($data, $from_encoding = 'UTF-8', $to_encoding = '') {
		if (!$to_encoding) $to_encoding = $this->outputEncoding;
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($data, $to_encoding, $from_encoding);
		} else {
			/*L::loadClass('Chinese', 'utility/lang', false);
			$chs = new Chinese($db_charset, $to_encoding);
			return $chs->Convert($data);*/
		}
		return $data;
	}
	
	private function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30) {
		//Copyright (c) 2003-2103 phpwind
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port > 0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http://', 'https://'), array('', 'ssl://'), $parse['scheme'] . "://") . $parse['host'];
		if (!$fp = @fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) return false;
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\', '//'), '/', $parse['path']) . "?" . $parse['query'];
		if ($method == 'GET') {
			$separator = $parse['query'] ? '&' : '';
			substr($data,0,1) == '&' && $data = substr($data,1);
			$parse['path'] .= $separator.$data;
		} elseif ($method == 'POST') {
			$wlength = "Content-length: " . strlen($data) . "\r\n";
			$wdata = $data;
		}
		$write = "{$method} $parse[path] HTTP/1.0\r\nHost: $parse[host]\r\nContent-type: application/x-www-form-urlencoded\r\n{$wlength}Connection: close\r\n\r\n{$wdata}";
		@fwrite($fp, $write);
		while ($data = @fread($fp, 4096)) {
			$responseText .= $data;
		}
		@fclose($fp);
		empty($showagent) && $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");
		return $responseText;
	}
}
