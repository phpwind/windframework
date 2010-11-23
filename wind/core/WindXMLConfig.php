<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
L::import('WIND:core.base.impl.WindConfigImpl');
L::import('WIND:utility.xml.xml');

/**
 * xml格式配置文件的解析类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindXMLConfig extends XML implements WindConfigImpl {
	private $xmlArray;
    private $childConfig;
	/**
	 * 构造函数，设置输出编码及变量初始化
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($data = '', $encoding = 'gbk') {
		parent::__construct($data, $encoding);
		$this->xmlArray = array();
		$this->setChildConfig();
	}
    
	/**
	 * 配置标签下的子标签集
	 * 
	 */
	private function setChildConfig() {
		$_config = array();
		//关于应用的配置
		$_config[WindConfigImpl::APP] = array(
					WindConfigImpl::APPNAME, 
					WindConfigImpl::APPROOTPATH, 
					WindConfigImpl::APPCONFIG, 
					WindConfigImpl::APPAUTHOR);
		//关于过滤器的配置
		/* 
		 * secondNodes: 代表了该标签的子级标签
		 * keyNodes: 代表了该标签的内容将作为键保存
		 * valueNodes: 代表了该标签的内容将作为值保存
		 */			
		$_config[WindConfigImpl::FILTERS] = array(
		            'secondNodes' => array(WindConfigImpl::FILTER),
		            'keyNodes' => array(WindConfigImpl::FILTERNAME),
		            'valueNodes' => array(WindConfigImpl::FILTERPATH));
		//配置视图相关
		$_config[WindConfigImpl::TEMPLATE] = array(
					WindConfigImpl::TEMPLATEDIR, 
					WindConfigImpl::COMPILERDIR, 
					WindConfigImpl::CACHEDIR, 
					WindConfigImpl::TEMPLATEEXT, 
					WindConfigImpl::ENGINE);
		//配置路由相关
	    $_config[WindConfigImpl::URLRULE] = array(
	    			WindConfigImpl::ROUTERPASE);
	    			
		$this->childConfig = $_config;
	}
	/**
	 * 返回解析的结果
	 * @param boolean $isCheck 是否需要检查配置
	 * @return array 返回解析后的数据信息
	 */
	public function getResult($isCheck = true) {
		return $this->fetchContents($isCheck);
	}
    
	/**
	 * 内容解析
	 * 
	 * 内容的解析依赖于配置文件中配置项的格式进行，每个配置项对应的在WindConfigImpl中都必须有对应的常量声明
	 * 对应的解析格式调用对应的解析函数。
	 * 
	 * @access private 
	 * @param boolean $isCheck 是否需要检查配置
	 * @return array
	 */
	private function fetchContents($isCheck = true) {
		$app = $this->createParser()->getElementByXPath(WindConfigImpl::APP);
		if ($isCheck && !$app) throw new WindException('the app config must be setting');
		$this->xmlArray[WindConfigImpl::APP] = $this->getSecondChildTree(WindConfigImpl::APP, $this->childConfig[WindConfigImpl::APP]);
		if ($isCheck && empty($this->xmlArray[WindConfigImpl::APP][WindConfigImpl::APPCONFIG]))  throw new WindException('the "appconfig" of the "app" config must be setted!');

		$this->xmlArray[WindConfigImpl::ISOPEN] = $this->getNoChild(WindConfigImpl::ISOPEN);
		$this->xmlArray[WindConfigImpl::DESCRIBE] = $this->getNoChild(WindConfigImpl::DESCRIBE);

		$this->xmlArray[WindConfigImpl::FILTERS] = $this->getThirdChildTree(WindConfigImpl::FILTERS, 
																			$this->childConfig[WindConfigImpl::FILTERS]['secondNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['keyNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['valueNodes']);

		$this->xmlArray[WindConfigImpl::TEMPLATE] = $this->getSecondChildTree(WindConfigImpl::TEMPLATE, $this->childConfig[WindConfigImpl::TEMPLATE]);
		$this->xmlArray[WindConfigImpl::URLRULE] = $this->getSecondChildTree(WindConfigImpl::URLRULE, $this->childConfig[WindConfigImpl::URLRULE]);
		return $this->xmlArray;
	}
    
	/**
	 * 获得单个的配置项
	 * @param string $node
	 * @return string
	 */
	private function getNoChild($node) {
		$dom = $this->getElementByXPath($node);
		if (!isset($dom[0])) return '';
		$contents = $this->getTagContents($dom[0]);
		return $contents['value'];
	}
    
	/**
	 * 获得有子配置项的配置项
	 * 包括该配置项及旗下所有的子配置项
	 * @param string $parentNode  需要查找的配置项
	 * @param array $nodes   该配置项下的所有子配置项
	 */
	private function getSecondChildTree($parentNode, $nodes) {
		if (!$nodes || !$parentNode) return array();
		(!is_array($nodes)) && $nodes = array($nodes);
		$dom = $this->getElementByXPath($parentNode);
		if (!$dom) return array();
		$childs = $this->getChilds($dom[0]);
		$_result = array();
		foreach ($childs as $child) {
			(in_array($child['tagName'], $nodes)) && $_result[$child['tagName']] = $child['value'];
		}
		return $_result;
	}
    
	/**
	 * 获得含有三级子配置项的配置项树
	 * 并且第三级的配置项中第一个子配置项作为key，第二个子配置项作为value，例如xml中filters配置项
	 * <pre>
	 * <filters>
	 *    <filter>
	 *    	 <filtername>filte1</filtername>
	 *       <filterpath>/filter1.php</filtername>
	 *    </filter>
	 *    <filter>
	 *       <filtername>filter2</filtername>
	 *       <filterpath>/filter2.php</filterpath>
	 *    </filter>
	 * </filters>
	 * </pre>
	 * 该方法对上述的这种情形，根据需求会解析出最后的结果是：
	 * $filters = array(
	 *       'filte1' => '/filter1.php',
	 *       'filter2' => '/filter2.php',
	 * )
	 *  
	 * @access private
	 * @param string $parentNode   当前配置项
	 * @param array $secondeParentNode  该配置项下的子配置项
	 * @param array $keyNode  将作为键的配置项
	 * @param array $valueNode 将作为值的配置项
	 * @return array 
	 */
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
				if (!in_array($_second['tagName'], $keyNode) && !in_array($_second['tagName'], $valueNode)) continue;
				in_array($_second['tagName'], $keyNode) && $_keys[] = $_second['value'];
				in_array($_second['tagName'], $valueNode) && $_values[] = $_second['value'];
			}
			$_childs = array_merge($_childs, array_combine($_keys, $_values));
		}
		return $_childs;
	}
    
	/**
	 * 创建解析器
	 * @access private
	 * @return XML object
	 */
	private function createParser() {
		if (is_object($this->object)) return $this;
		$this->ceateParser();
		return $this;
	}
}
