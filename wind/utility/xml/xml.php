<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * xml解析的工具
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class XML {
	/**
	 *  需要解析的数据
	 *  
	 * @var string
	 */
	protected $XMLData; 
	/**
	 * 建立解析的对象
	 * 
	 * @var SimpleXMLElement
	 */
	protected $object;
	/**
	 * 解析输出的编码
	 * 
	 * @var string
	 */
	protected $outputEncoding;
	
	/**
	 * 构造函数,初始化对象
	 * 
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($data = '', $encoding = 'gbk') {
		$this->setXMLData($data);
		$this->setOutputEncoding($encoding);
	}
	
	/**
	 * 设置需要解析的xml内容
	 * 
	 * @param string $data
	 */
	public function setXMLData($data) {
		if (!$data) return false;
		if ($this->isXMLFile($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('输入参数不是有效的xml格式');
		}
	}
	/**
	 * 设置解析输出的编码
	 * 
	 * @param string $encoding
	 */
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
		if (!is_file($filePath) || strtolower(substr($filePath, -4)) != '.xml') throw new Exception("The file which your put is not a well-format xml file!");
		$this->setXMLData(file_get_contents($filePath));
	}
	
	/**
	 * 是否为xml格式文件
	 * 
	 * @access private
	 * @return boolean
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
	
	/**
	 * 创建解析对象
	 */
	public function ceateParser() {
   		$this->object = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	/**
	 * 返回解析对象
	 * 
	 * @return SimpleXMLElement
	 */
	public function getXMLDocument() {
		return $this->object;
	}
	
	/**
	 * 根据标签的路径获得该标签的对象
	 * 
	 * 如下格式<pre/>:
	 * <WIND>
	 *    <app>
	 *       <appName>Test</appName>
	 *    </app>
	 * </WIND>
	 * 1：采用相对路径调用：
	 * 		如要获得app下的内容则如此调用：  $xmlObject->getElementByXPath('app');
	 * 		如要获得app下的appName的内容则如此调用：$xmlObject->getElementByXPath('app/appName');
	 * 2：采用完全路径调用：
	 * 		如要获得app下的内容则如此调用：  $xmlObject->getElementByXPath('/WIND/app');
	 * 		如要获得app下的appName的内容则如此调用：$xmlObject->getElementByXPath('/WIND/app/appName');
	 * 
	 * @param string $tagPath
	 * @return array SimpleXMLElement objects 
	 */
	public function getElementByXPath($tagPath) {
		if ($tagPath) return $this->object->xpath($tagPath);
	}
	
	/**
	 * 输入通过getElementByXPath获得的对象集合,解析输出对应的数组
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 				  'value' => '对应标签的内容',
	 * 				  'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 *                'children' => array(child1, child2,....);
	 * 
	 * 
	 * @param array SimpleXMLElement objects   $elements
	 * @return array
	 */
    public function getContentsList($elements) {
    	(!is_array($elements)) && $elements = array($elements);
    	$_result = array();
    	foreach ($elements as $key => $element) {
    		$_result[] = self::getTagContents($element);
    	}
    	return $_result;
    }
    
    /**
	 * 将输入SimpleXMLElement对象,解析输出对应的内容及其子标签
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 				  'value' => '对应标签的内容',
	 * 				  'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 *                'children' => array(child1, child2,....);
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getTagContents($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = self::getValue($element);
		$_array['attributes'] = self::getAttributes($element);
		$_array['children'] = self::getChildren($element);
		return $_array;
	}
	
	/**
	 * 获得当前对象的内容
	 * 将输入SimpleXMLElement对象,解析输出其对应的内容（不包含子标签）
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 				  'value' => '对应标签的内容',
	 * 				  'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 * 			)
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getCurrent($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = self::getValue($element);
		$_array['attributes'] = self::getAttributes($element);
		return $_array;
	}
	
	/**
	 * 获得该标签的内容
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getValue($element) {
		if ($element[0]) return self::escape($element[0]);
		return '';
	}
	
	/**
	 * 获得该标签的内容
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getTagName($element) {
		return $element->getName();
	}
	
	/**
	 * 判断该元素是否有属性
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function hasAttributes($element) {
		if ($element->attributes()) return true;
		return false;
	}
	
	/**
	 * 返回节点的属性
	 * 使用XML::getAttributes($element);
	 * 返回的格式为：
	 * $array = array('属性名字' => '属性值', ... );
	 * 
	 * @param SimpleXMLElement $element
	 * @return array  返回该节点的属性
	 */
	public function getAttributes($element) {
		$_attributes = array();
		$attributes = $element->attributes();
		if (!$attributes) return $_attributes;
		foreach ($attributes as $key => $value) {
			$_attributes[$key] = self::escape($value);
		}
		return $_attributes;
	}
	
	/**
	 * 判断该元素是否有子标签
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function hasChildren($element) {
		if ($element->children()) return true;
		return false;
	}
	
	/**
	 * 获得指定标签下的所有子标签
	 * 
	 * @param SimpleXMLElement $element
	 * @return array 
	 */
	public function getChildren($element) {
		$_childs = array();
		$childs = $element->children();
		if (!$childs) return $_childs;
		foreach ($childs as $key => $value) {
			$_childs[] = self::getTagContents($value);
		}
		return $_childs;
	}
	
	/**
	 * 给输出结果进行转码（根据设置的输出编码进行转换）
	 * 
	 * @access private
	 * @param string $param
	 * @return string
	 */
	public function escape($param) {
		return self::dataConvert(strval($param));
	}
		
	/**
	 * 将输入的内容进行转码输出
	 * 
	 * @param string $data
	 * @param string $from_encoding
	 * @param string $to_encoding
	 * @return string
	 */
	protected function dataConvert($data, $from_encoding = 'UTF-8', $to_encoding = '') {
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
	
	/**
	 * 从给定的一个网址中获得xml内容
	 * 
	 * @access private
	 * @param string $host
	 * @param string $data
	 * @param string $method
	 * @param string $showagent
	 * @param string $port
	 * @param integer $timeout
	 * @return string 
	 */
	private function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30) {
		//Copyright (c) 2003-2103 phpwind
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port > 0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http:\/\/', 'https:\/\/'), array('', 'ssl:\/\/'), $parse['scheme'] . ":\/\/") . $parse['host'];
		if (!$fp = @fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) return false;
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\', '\/\/'), '/', $parse['path']) . "?" . $parse['query'];
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
