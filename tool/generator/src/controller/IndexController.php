<?php
Wind::import('WEB:src.WindGenerateProject');
/**
 * web模式
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package tool
 * @subpackage generator
 */
class IndexController extends WindController {
	/**
	 * @var WindGenerateProject
	 */
	private $project;
	private $config = 'WEB:conf.project.default';
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$this->project = new WindGenerateProject();
		$this->project->name = 'test';
		$this->project->dir = Wind::getRealDir('PROJ:' . $this->project->name);
		$this->project->setConfig($this->config);
		$r = $this->project->generate();
		if (!$r) echo 'generate fail';
		else echo 'generate success';
	}
	
	public function doRun() {
		
	}
}

?>