<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.base.impl.WindConfigImpl');
L::import('WIND:utility.xml.xml');
class WindXMLConfig extends XML implements WindConfigImpl {
	private $xmlArray;
	
	public function __construct($data = '', $encoding = 'gbk') {
		parent::__construct($data, $encoding);
		$this->xmlArray = array();
	}
	
	public function getResult() {
		return $this->fetchContents();
	}
	
	private function fetchContents() {
		$app = $this->createParser()->getElementByXPath(WindConfig::app);
		if (!$app) throw new Exception('Ó¦ÓÃÅäÖÃ±ØĞëÅäÖÃ');
		$_temp = array(WindConfig::appName, WindConfig::appPath, WindConfig::appConfig);
		$this->xmlArray[WindConfig::app] = $this->getSecondChildTree(WindConfig::app, $_temp);
		
		$this->xmlArray[WindConfig::isOpen] = $this->getNoChild(WindConfig::isOpen);		
		$this->xmlArray[WindConfig::describe] = $this->getNoChild(WindConfig::describe);
		
		$this->xmlArray[WindConfig::filters] = $this->getThirdChildTree(WindConfig::filters, WindConfig::filter, WindConfig::filterName, WindConfig::filterPath);
		
		$_temp = array(WindConfig::templateDir, WindConfig::compileDir, WindConfig::cacheDir, WindConfig::templateExt, WindConfig::engine);		
		$this->xmlArray[WindConfig::template] = $this->getSecondChildTree(WindConfig::template, $_temp);
		$this->xmlArray[WindConfig::urlRule] = $this->getSecondChildTree(WindConfig::urlRule, WindConfig::routerPase);
		return $this->xmlArray;
	}
	
	private function getNoChild($node) {
		$dom = $this->getElementByXPath($node);
		if ($dom) return $this->escape(strval($dom[0]));
	}
	
	private function getSecondChildTree($parentNode, $nodes) {
		if (!$nodes || !$parentNode) return array(); 
		(!is_array($nodes)) && $nodes = array($nodes);
		$dom = $this->getElementByXPath($parentNode);
		if (!$dom) return array();
		$childs = $this->getChilds($dom[0]);
		$_result = array();
		foreach ($childs as $child) {
			(in_array($child['tagName'], $nodes)) && $_result[$child['tagName']] = $this->escape($child['value']);
		}
		return $_result;
	}
	
	private function getThirdChildTree($parentNode, $secondeParentNode, $keyNode, $valueNode) {
		if (!$parentNode || !$secondeParentNode) return array(); 
		(!is_array($keyNode)) && $keyNode = array($keyNode);
		(!is_array($valueNode)) && $valueNode = array($valueNode);
		(!is_array($secondeParentNode)) && $secondeParentNode = array($secondeParentNode);
		$dom = $this->getElementByXPath($parentNode);
		$childs = $this->getChilds($dom[0]);
		$_childs = array();
		foreach($childs as $child) {
			if (!in_array($child['tagName'], $secondeParentNode)) continue;
			$_secondeChild = $child['children'];
			$_keys = array();
			$_values = array();
			foreach ($_secondeChild as $_key => $_second) {
				if (!in_array($_second['tagName'], $keyNode) && !in_array($_second['tagName'], $valueNode)) unset($_secondeChild[$_key]);
				in_array($_second['tagName'], $keyNode) && $_keys[] = $_second['value'];
				in_array($_second['tagName'], $valueNode) && $_values[] = $this->escape($_second['value']);
			}
			$_childs = array_merge($_childs, array_combine($_keys, $_values));
		}
		return $_childs;
	}
	
	private function createParser() {
		if (is_object($this->object)) return $this;
		$this->doParser();
		return $this;
	}
	
	private function escape($param) {
		$param = $this->dataConvert($param);
		if (is_string($param)) return "'" . $param . "'";
		if (is_array($param)) {
			foreach ($param as $key => $value) {
				$key = "'" . $key . "'";
				$value = $this->escape($value);
				unset($param[$key]);
				$param[$key] = $value;
			}
		}
		return $param;
	}
	
}
