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
		$app = $this->createParser()->getElementByXPath(WindConfigImpl::APP);
		if (!$app) throw new Exception('the app config must be setting');
		$_temp = array(WindConfigImpl::APPNAME, WindConfigImpl::APPROOTPATH, WindConfigImpl::APPCONFIG);
		$this->xmlArray[WindConfigImpl::APP] = $this->getSecondChildTree(WindConfigImpl::APP, $_temp);
		if (empty($this->xmlArray[WindConfigImpl::APP][WindConfigImpl::APPCONFIG]))  throw new Exception('the "appconfig" of the "app" config must be setted!');

		$this->xmlArray[WindConfigImpl::ISOPEN] = $this->getNoChild(WindConfigImpl::ISOPEN);
		$this->xmlArray[WindConfigImpl::DESCRIBE] = $this->getNoChild(WindConfigImpl::DESCRIBE);

		$this->xmlArray[WindConfigImpl::FILTERS] = $this->getThirdChildTree(WindConfigImpl::FILTERS, WindConfigImpl::FILTER, WindConfigImpl::FILTERNAME, WindConfigImpl::FILTERPATH);

		$_temp = array(WindConfigImpl::TEMPLATEDIR, WindConfigImpl::COMPILERDIR, WindConfigImpl::CACHEDIR, WindConfigImpl::TEMPLATEEXT, WindConfigImpl::ENGINE);
		$this->xmlArray[WindConfigImpl::TEMPLATE] = $this->getSecondChildTree(WindConfigImpl::TEMPLATE, $_temp);
		$this->xmlArray[WindConfigImpl::URLRULE] = $this->getSecondChildTree(WindConfigImpl::URLRULE, WindConfigImpl::ROUTERPASE);
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
		if (!isset($dom[0])) return array(); 
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
		return $param;
	}

}
