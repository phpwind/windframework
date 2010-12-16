<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * xml文件解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindXmlParser{
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
	public function __construct($version = '1.0', $encode = 'utf-8'){
		$this->dom = new DOMDocument($version,$encode);
	}
	
	/**
	 * @param string $filename xml 文件名
	 * @param int $option 解析选项
	 * @return array
	 */
	public function parse($filename,$option = null){
		if(!is_file($filename)){
			return false;
		}
		$this->dom->load($filename,$option);
		$root = $this->dom->documentElement;
		return $this->buildData($root,$root->nodeName);
		
	}
	
	/**
	 * @param DOMNodeList $node 要解析的XMLDOM节点
	 * @param stiring $lastNodeName 上一个XMLDOM节点名称
	 * @param array $data 要解析XMLDOM节点的子节点
	 * @param array $lastData 上一个XMLDOM节点下的子节点
	 * @return array 返回解析后的值 
	 */
	public function buildData($node,$lastNodeName ,&$data = array(),&$lastData=array()){
		if($node->hasChildNodes()){
			foreach($node->childNodes as $node){
				if(3 == $node->nodeType && trim($node->nodeValue)){
					$lastData[$lastNodeName] = $node->nodeValue;
				}
				if(1 == $node->nodeType){
					$nodeName = ($name = $node->getAttribute(self::NAME)) ? $name : $node->nodeName;
					$this->buildData($node,$nodeName,$data[$nodeName],$data);
				}
			}
		}
		return $data;
	}	
}