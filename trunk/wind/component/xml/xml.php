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
	 * 需要解析的数据
	 * 
	 * @var string
	 */
	protected $XMLData;
	/**
	 * 建立解析的对象
	 * 
	 * @var SimpleXMLElement
	 */
	protected $parse;
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
	public function __construct($data = '', $encoding = 'UTF-8') {
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
		if ($this->isXMLData($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('The file which your put is not a well-format xml file!');
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
	public function getXMLFromFile($filePath) {
		$filePath = trim($filePath);
		if (!is_file($filePath) || (strtolower(strrchr($filePath, '.')) != '.xml')) throw new Exception("The file which your put is not a well-format xml file!");
		$this->setXMLData(file_get_contents($filePath));
	}

	/**
	 * 根据指定URL读取XML数据
	 *
	 * @param string $url
	 */
	public function getXMLFromUrl($url) {
		//TODO
	}
	
	/**
	 * 是否为xml格式文件
	 * 
	 * @access private
	 * @return boolean
	 */
	private function isXMLData($data) {
		if (strpos(strtolower(trim($data)), '<?xml') === false) {
			return false;
		}
		return true;
	}
	
	/**
	 * 创建解析对象
	 */
	public function ceateParser() {
		$this->parse = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	/**
	 * 返回解析对象
	 * 
	 * @return SimpleXMLElement
	 */
	public function getXMLDocument() {
		return $this->parse;
	}
	
	/**
	 * 根据标签的路径获得该标签的对象
	 * 
	 * 如下格式<pre/>:
	 * <WIND>
	 * <app>
	 * <appName>Test</appName>
	 * </app>
	 * </WIND>
	 * 1：采用相对路径调用：
	 * 如要获得app下的内容则如此调用：  $xmlObject->getElementByXPath('app');
	 * 如要获得app下的appName的内容则如此调用：$xmlObject->getElementByXPath('app/appName');
	 * 2：采用完全路径调用：
	 * 如要获得app下的内容则如此调用：  $xmlObject->getElementByXPath('/WIND/app');
	 * 如要获得app下的appName的内容则如此调用：$xmlObject->getElementByXPath('/WIND/app/appName');
	 * 
	 * @param string $tagPath
	 * @return array SimpleXMLElement objects 
	 */
	public function getElementByXPath($tagPath) {
		if (trim($tagPath)) return $this->parse->xpath($tagPath);
	}
	
	/**
	 * 输入通过getElementByXPath获得的对象集合,解析输出对应的数组
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 'value' => '对应标签的内容',
	 * 'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 * 'children' => array(child1, child2,....);
	 * 
	 * 
	 * @param SimpleXMLElement objects   $elements
	 * @return array
	 */
	public function parseElement($elements) {
		$result = array();
		foreach ($elements as $key => $element) {
			$result[] = $this->getTagContents($element);
		}
		return $result;
	}
	
	/**
	 * 将输入SimpleXMLElement对象,解析输出对应的内容及其子标签
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 'value' => '对应标签的内容',
	 * 'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 * 'children' => array(child1, child2,....);
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getTagContents($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
		$result = array();
		$result['tagName'] = $element->getName();
		$result['value'] = $this->getValue($element);
		$result['attributes'] = $this->getAttributes($element);
		$result['children'] = $this->getChildren($element);
		return $result;
	}
	
	/**
	 * 获得当前对象的内容
	 * 将输入SimpleXMLElement对象,解析输出其对应的内容（不包含子标签）
	 * 
	 * 每个元素都有格式
	 * $array = array('tagName' => '该标签的名字',
	 * 'value' => '对应标签的内容',
	 * 'attributes' => array('标签属性的名称' => '该属性对应的值', ...),
	 * )
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getCurrent($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
		$result = array();
		$result['tagName'] = $element->getName();
		$result['value'] = $this->getValue($element);
		$result['attributes'] = $this->getAttributes($element);
		return $result;
	}
	
	/**
	 * 获得该标签的内容
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getValue($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
		if (isset($element[0])) return $this->escape($element[0]);
		return '';
	}
	
	/**
	 * 获得该标签的内容
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getTagName($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
		return $element->getName();
	}
	
	/**
	 * 判断该元素是否有属性
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function haveAttributes($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
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
	public function getAttributes($node) {
		if (!($node instanceof SimpleXMLElement)) return array();
		if (!($attributeList = $node->attributes())) return array();
		$attributes = array();
		foreach ($attributeList as $key => $value) {
			$attributes[$key] = $this->escape($value);
		}
		return $attributes;
	}
	
	/**
	 * 判断该元素是否有子标签
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function haveChildren($element) {
		if (!($element instanceof SimpleXMLElement)) return '';
		if ($element->children()) return true;
		return false;
	}
	
	/**
	 * 获得指定标签下的所有子标签
	 * 
	 * @param SimpleXMLElement $element
	 * @return array 
	 */
	public function getChildren($node) {
		if (!($node instanceof SimpleXMLElement)) return array();
		if (!($childList = $node->children())) return array();
		$childs = array();
		foreach ($childList as $key => $value) {
			$childs[] = $this->getTagContents($value);
		}
		return $childs;
	}
	
	/**
	 * 给输出结果进行转码（根据设置的输出编码进行转换）
	 * 
	 * @access private
	 * @param string $param
	 * @return string
	 */
	public function escape($param) {
		return trim($this->dataConvert(strval($param)));
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
		if ($from_encoding == $to_encoding) return $data;
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($data, $to_encoding, $from_encoding);
		} else {
			/*L::loadClass('Chinese', 'utility/lang', false);
			$chs = new Chinese($db_charset, $to_encoding);
			return $chs->Convert($data);*/
		}
		return $data;
	}
}
