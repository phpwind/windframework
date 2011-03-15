<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * xml文件解析
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindXmlParser {

	/**
	 * @var string 节点名称
	 */
	const NAME = 'name';

	/**
	 * @var Domdocument DOM解析器
	 */
	private $dom = null;

	/**
	 * @param string $version xml版本
	 * @param string $encode  xml编码
	 */
	public function __construct($version = '1.0', $encode = 'utf-8') {
		if (!class_exists('DOMDocument')) throw new WindException('DOMDocument is not exist.');
		$this->dom = new DOMDocument($version, $encode);
	}

	/**
	 * @param string $filename xml 文件名
	 * @param int $option 解析选项
	 * @return array
	 */
	public function parse($filename, $option = null) {
		if (!is_file($filename)) return array();
		$this->dom->load($filename, $option);
		return $this->getChilds($this->dom->documentElement);
	}

	/**
	 * 获得节点的所有子节点
	 * 
	 * 子节点包括属性和子节点（及文本节点),
	 * 子节点的属性将会根据作为该节点的一个属性元素存放，如果该子节点中含有标签列表，则会进行一次合并。
	 * 每个被合并的列表项都作为一个单独的数组元素存在。
	 * 
	 * @param DOMElement $node 要解析的XMLDOM节点
	 * @return array 返回解析后该节点的数组
	 */
	public function getChilds($node) {
		if (!$node instanceof DOMElement) return array();
		$childs = array();
		foreach ($node->childNodes as $node) {
			$tempChilds = $attributes = array();
			($node->hasAttributes()) && $attributes = $this->getAttributes($node);
			(3 == $node->nodeType && trim($node->nodeValue)) && $childs[0] = (string) $node->nodeValue;
			if (1 !== $node->nodeType) continue;
			
			$nodeName = ($name = $node->getAttribute(self::NAME)) ? $name : $node->nodeName;
			$tempChilds = $this->getChilds($node);
			$tempChilds = array_merge($attributes, $tempChilds);
			if (empty($tempChilds)) $tempChilds = '';
			
			$tempChilds = (isset($tempChilds[0]) && count($tempChilds) == 1) ? $tempChilds[0] : $tempChilds;
			if (!isset($childs[$nodeName])) {
				$childs[$nodeName] = $tempChilds;
				continue;
			} else {
				$element = $childs[$nodeName];
				$childs[$nodeName] = (is_array($element) && !is_numeric(implode('', array_keys($element)))) ? array_merge(array(
					$element), array($tempChilds)) : array_merge((array) $element, array($tempChilds));
				continue;
			}
		}
		return $childs;
	}

	/**
	 * 获得节点的属性
	 * 
	 * 该属性将不包含属性为name的值--规则（name的值将作为解析后数组的key值索引存在）
	 * 
	 * @param DOMElement $node
	 * @return array 返回属性数组
	 */
	public function getAttributes($node) {
		if (!$node instanceof DOMElement || !$node->hasAttributes()) return array();
		$attributes = array();
		foreach ($node->attributes as $attribute) {
			if (self::NAME != $attribute->nodeName) {
				$attributes[$attribute->nodeName] = (string) $attribute->nodeValue;
			}
		}
		return $attributes;
	}
}
?>