<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
L::import('WIND:component.config.base.IWindParser');
L::import('WIND:component.xml.xml');

/**
 * xml格式配置文件的解析类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$
 * @package
 */
class WindXMLConfig extends XML implements IWindParser {
	/**
	 * 定义允许拥有的属性
	 * name: 可以定义一些列的item中每一个item的名字以区分每一个
	 * isGlobal: 如果添加上该属性，则该标签将在解析完成之后被提出放置在全局缓存中 -----只作用于一级标签
	 * isMerge: 如果添加上该属性，则该标签将被在解析后进行合并 -----只作用于一级标签
	 */
	const NAME = 'name';
	const ISGLOBAL = 'isGlobal';
	const ISMERGE = 'isMerge';
	
	private $xmlArray;
	private $globalTags = array();
	private $mergeTags = array();
	/**
	 * 构造函数，设置输出编码及变量初始化
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($encoding = 'UTF-8') {
		$this->setOutputEncoding($encoding);
	}
	
	/**
	 * 加载需要解析的文件
	 * @param unknown_type $filename
	 */
	public function loadFile($filename) {
		$this->getXMLFromFile($filename);
	}
	
	/**
	 * 根据读取的内容解析
	 * @param unknown_type $filename
	 */
	public function loadXMLString($xmlString) {
		$this->setXMLData(trim($xmlString));
	}
	
	/**
	 * 内容解析
	 *
	 * 内容的解析依赖于配置文件中配置项的格式进行，每个配置项对应的在IWindConfig中都必须有对应的常量声明
	 * 对应的解析格式调用对应的解析函数。
	 *
	 * @return boolean
	 */
	public function parser() {
		$this->ceateParser();
		$children = $this->getXMLDocument()->children();
		$_array = array();
		foreach ($children as $node => $child) {
			$elements = (array) $this->getElementByXPath($node);
			foreach ($elements as $element) {
				list($key, $value) = $this->getContents($element);
				if (($value = $this->checkValue($value)) !== false) $_array[$key] = $value;
			}
		}
		$this->xmlArray = $_array;
		return true;
	}
	
	/*
	 * 返回解析的结果
	 * @return array 返回解析后的数据信息
	 */
	public function getResult() {
		if (!$this->xmlArray) $this->parser();
		return $this->xmlArray;
	}

	/**
	 * 返回需要设置全局的标签集
	 * 
	 * @return array; 
	 */
	public function getGlobalTags() {
		return $this->globalTags;
	}
	
	/**
	 * 返回需要设置合并的标签集
	 * 
	 * @return array; 
	 */
	public function getMergeTags() {
		return $this->mergeTags;
	}
	
	/**
	 * 根据返回节点内容
	 *
	 * 获得含有属性和子标签的标签内容，规则如下<pre/>:
	 * <bbbb name='aaa1' attrib1='dddd'>
	 * 		<filterName>windFilter1</filterName>
	 * 		<filterPath>/filter1</filterPath>
	 * </bbbb>
	 * 该方法对上述的这种情形，根据需求会解析出最后的结果是：
	 * return array(aaa1,
	 * 		        array(name => aaa1,
	 * 			 		attrib1 => dddd,
	 * 		     		filterName => windFilter1,
	 *           		filterPath => /filter1,
	 *           		tagName = bbbb,
	 *       		)
	 * 			)
	 *
	 *<tag>value</tag>
	 * 并且返回形式为array(tag, value)
	 * 
	 * @param SimpleXMLElement $element
	 * @return array
	 */
	private function getContents($node) {
		$hasAttr = $this->haveAttributes($node);
		$hasChild = $this->haveChildren($node);
		if ($hasAttr && $hasChild) {
			list($tag) = $this->getAttributesList($node);
			list(, $childValue) = $this->getChildrenList($node);
			return array($tag, $childValue);
		}
		if ($hasChild) {
			return $this->getChildrenList($node);
		}
		if ($hasAttr) {
			return $this->getAttributesList($node);
		}
		return array($node->getName(), trim($this->getValue($node)));
	}
	
	/**
	 * 获得含有子标签的标签内容：
	 * <AA>
	 * <BB name='key1' value='key1Value' attri3='attribute1'/>
	 * <BB value='key2Value' attri3='attribute2'/>
	 * </AA>
	 * 如果含有属性name，则将该name作为key
	 * 返回结果array(AA, array(key1 => array(tagName = BB, name => key1, value=>key1Value, attri3 => attribute1),
	 * BB => array(tagName => BB, value=>key2Value, attri3 => attribute2)
	 * ))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getAttributesList($node) {
		$tag = $node->getName();
		$attributes = $this->getAttributes($node);
		$attributes['tagName'] = $tag;
		(isset($attributes[self::NAME])) && $tag = $attributes[self::NAME];
		$this->setGlobalAndMergeTags($attributes);
		return array($tag, $attributes);
	}
	
	/**
	 * 获得含有子标签的标签内容：
	 * <AA>
	 * <BB>Bvalue</BB>
	 * <CC>Cvalue</CC>
	 * </AA>
	 * 返回结果array(AA, array(BB => Bvalue, CC => Cvalue))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getChildrenList($node) {
		$tag = $node->getName();
		$childArray = array();
		foreach ($node->children() as $child) {
			list($childTag, $childValue) = $this->getContents($child);
			if (($value = $this->checkValue($childValue)) !== false) $childArray[$childTag] = $value;
		}
		if (($value = $this->getValue($node)) != '') {
			(count($childArray) == 0) ? $childArray = $value : $childArray[] = $value;
		}
		return array($tag, $childArray);
	}
	
	/**
	 * 设置全局的标签和需要合并的标签
	 * 
	 * @param array $attributes
	 * @return boolean; 
	 */
	private function setGlobalAndMergeTags($attributes) {
		$tag = $attributes['tagName'];
		$name = isset($attributes[self::NAME]) ? $attributes[self::NAME] : $tag;
		(isset($attributes[self::ISGLOBAL]) && $attributes[self::ISGLOBAL] == 'true') && $this->globalTags[$name] = $tag;
		(isset($attributes[self::ISMERGE]) && $attributes[self::ISMERGE] == 'true') && $this->mergeTags[$name] = $tag;
		return true;
	}
	
	/**
	 * 检查是否为空，如果为空返回false 否则返回格式化后的值
	 * 
	 * @param mixed $value
	 * @return mixed boolean | 
	 */
	private function checkValue($value) {
		if (is_string($value)) {
			return (trim($value) == '') ? false : trim($value);
		}
		if (is_array($value)) {
			return (count($value) == 0) ? false : $value;
		}
	}
}
