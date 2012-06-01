<?php
Wind::import('WIND:command.WindCommandController');
Wind::import('WEB:src.WindGenerateProject');
/**
 * 快速创建工程
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package tool
 * @subpackage generator
 */
class IndexCommand extends WindCommandController {
	/**
	 * @var WindGenerateProject
	 */
	private $project;
	private $config = 'COMMAND:conf.project.default';
	/*
	 * 主流程 (non-PHPdoc) 
	 * 
	 * @see WindCommandController::run()
	 */
	public function run($projName = 'test') {
		list($projName, $dir) = $this->_checkDir($projName);
		$this->project = new WindGenerateProject();
		$this->chooseMode();
		$this->project->name = $projName;
		$this->project->dir = $dir;
		
		$result = $this->project->generate();
		if ($result === false) {
			$this->setOutput(<<<EOB
Generate Project $projName fail! 
The possible reason : write file failed.				
EOB
				);
		} else {
			$this->setOutput(<<<EOT
Generate Project $projName success! 
You can find it under {$this->project->dir}
EOT
			);
		}
	}
	
	/**
	 * 选择标准或者自定义模式
	 *
	 * @param WindGenerateProject $project
	 */
	protected function chooseMode() {
		$r = $this->getLine(<<<EOA
		欢迎使用wind framework 快速创建工具！
		分为‘自定义’、‘标准’两种模式。
		自定义模式：您可以自定义：目录结构、目录名称等
		标准模式：将会自动生成标准的工程目录
		请选择模式 (标准模式[Y] | 自定义模式[N]) ：
EOA
		);
		if (strtolower($r[0]) == 'y') {
			$this->project->setConfig($this->config);
		} else {
			$this->project->dataDir = $this->getLine('请输入你想要指定的缓存目录，默认是待创建工程目录下的data目录[data]：');
			$this->project->templateDir = $this->getLine('请输入你想要指定的模板目录，默认是待创建工程目录下的template目录[template]：');
			$this->project->srcDir = $this->getLine(
				'请输入你想要指定的业务逻辑处理目录，默认是待创建工程目录下的src/controller目录[src/controller]：');
			$this->project->confDir = $this->getLine('请输入你想要指定的配置目录，默认是待创建工程目录下的conf目录[conf]：');
			$this->project->wwwDir = $this->getLine('请输入你想要指定的web可访问资源目录，默认是待创建工程目录下的www目录[www]：');
		}
	}

	

	/**
	 * 检测路径
	 *
	 * @param string $projName        	
	 * @return string
	 */
	private function _checkDir($projName) {
		static $dir = '', $name = '';
		$name = $projName;
		$dir = Wind::getRealDir('PROJ:' . $name);
		if (is_dir($dir)) {
			$r = $this->getLine(
				"The Project Folder [$dir] Already Exist, Are you sure to override it?(Yes|No)");
			if (strtolower($r[0]) != 'y') {
				$name = $this->getLine('Please input the project name：');
				$this->_checkDir($name);
			}
		}
		return array($name, $dir);
	}
	
}

?>