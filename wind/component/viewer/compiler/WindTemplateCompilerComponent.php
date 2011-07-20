<?php

Wind::import('WIND:core.viewer.AbstractWindTemplateCompiler');

/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-20  xiaoxiao
 */
class WindTemplateCompilerComponent extends AbstractWindTemplateCompiler {

	protected $name = ''; //组件名字

	protected $args = '';//传递给组件的参数

	protected $templateDir = '';//组件调用的模板路径
	
	protected $appConfig = '';//组件的配置文件
	
	protected $componentPath = '';//组件的入口地址

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		return $this->getScript($content);
	}

	/**
	 * @return string
	 */
	private function getScript($content) {
		$params = $this->matchConfig($content);
		if (!isset($params['name']) || !isset($params['componentPath'])) throw new WindException('组件编译错误!');
		$content = "<?php\r\n";
		$content .= $this->rebuildConfig($params) . (isset($params['args']) ? $this->registerUrlParams($params) : '') .
				   "\$componentPath = Wind::getRealPath('" . $params['componentPath'] . "', true);\r\n" .
		           "Wind::register(\$componentPath, '" . $params['name'] . "');\r\n" .
				   "Wind::run('" . $params['name'] . "', \$config);\r\n?>";
		return $content;
	}
	
	/**
	 * 编译获得配置文件
	 * @param array $params
	 * @return array
	 */
	private function rebuildConfig($params) {
		$temp = "\$configParser = new WindConfigParser();\r\n" . 
			   "\$configPath = Wind::getRealPath('" . $params['appConfig'] . "');\r\n";
		$temp .= "\$config = \$configParser->parse(\$configPath, '" . $params['name'] . "');\r\n";
		if (!isset($params['templateDir'])) return $temp;
		if (isset($params['args']['m'])) $temp .= "\$config['web-apps']['" . $params['name'] . "']['modules']['" . $params['args']['m'] . "']['view']['config']['template-dir']['value'] = '" . $params['templateDir'] . "';\r\n";
		else {
			$temp .= "foreach(\$config['web-apps']['" . $params['name'] . "']['modules'] as \$key => \$value) {\r\n" .
				     "\t\$config['web-apps']['" . $params['name'] . "']['modules'][\$key]['view']['config']['template-dir']['value'] = '" . $params['templateDir'] . "';\r\n" .
			         "}\r\n";
		}
		return $temp;
	}
	
	/**
	 * 注册变量信息
	 * 
	 * @param array $params 
	 */
	private function registerUrlParams($params) {
		$temp = '';
		$temp = "\$mKey = isset(\$config['web-apps']['" . $params['name'] . "']['router']['config']['module']['url-param']) ? \$config['web-apps']['" . $params['name'] . "']['router']['config']['module']['url-param'] : 'm';\r\n" .
		        "\$cKey = isset(\$config['web-apps']['" . $params['name'] . "']['router']['config']['controller']['url-param']) ? \$config['web-apps']['" . $params['name'] . "']['router']['config']['controller']['url-param'] : 'c';\r\n" .
		        "\$aKey = isset(\$config['web-apps']['" . $params['name'] . "']['router']['config']['action']['url-param']) ? \$config['web-apps']['" . $params['name'] . "']['router']['config']['action']['url-param'] : 'a';\r\n" .
		        "\$_GET[\$mKey] = '" . $params['args']['m'] . "';\r\n" .
				"\$_GET[\$cKey] = '" . $params['args']['c'] . "';\r\n" .
				"\$_GET[\$aKey] = '" . $params['args']['a'] . "';\r\n";
	    unset($params['args']['a'], $params['args']['c'], $params['args']['m']);
		foreach($params['args'] as $key => $value) {
		     $temp .= is_array($value) ? "\$_GET['" . $key . "'] = " . $value . ";\r\n" : "\$_GET['" . $key . "'] = '" . $value . "';\r\n";
		}
		return $temp;
	}
	
	/**
	 * 匹配配置信息
	 * 
	 * @param string $content
	 * @return array
	 */
	private function matchConfig($content) {
		preg_match_all('/(\w+=[\'|"]?[\w|.|:]+[\'|"]?)/', $content, $mathcs);
		list($config, $key, $val) = array(array(), '', '');
		foreach ($mathcs[0] as $value) {
			list($key, $val) = explode('=', $value);
			if (!in_array($key, $this->getProperties()) || !$val) continue;
			switch($key) {
				case 'args':
				    $config['args'] = $this->compileArgs(trim($val, '\'"'));
				    break;
				default:
					$config[$key] = trim($val, '\'"');
					break;
			}
		}
		return $config;
	}
	
	/**
	 * 解析传递给url的参数信息
	 * 
	 * @param string $arg
	 * @return array
	 */
	private function compileArgs($arg) {
		$args = explode(':', $arg);
		$urlParams = array();
		list($urlParams['a'], $urlParams['c'], $urlParams['m']) = array('', '', '');
		switch(count($args)) {
			case 1:
			    $urlParams['a'] = $args[0];
			    break;
			case 2:
				list($urlParams['c'], $urlParams['a']) = $args;
				break;
			case 3:
				list($urlParams['m'], $urlParams['c'], $urlParams['a']) = $args;
				break;
			default;
				break;
		}
		return $urlParams;
	}
		

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	protected function getProperties() {
		return array('name', 'templateDir', 'appConfig', 'args', 'componentPath');
	}
}

?>