<?php
Wind::import('WIND:utility.WindFolder');
Wind::import('WIND:utility.WindFile');
/**
 * 生成工程流程
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package tool
 * @subpackage generator
 */
class WindGenerateProject {
	
	/**
	 * 工程目录
	 */
	public $dir;
	/**
	 * 工程名称
	 */
	public $name;
	/**
	 * 工程缓存目录 
	 */
	public $dataDir;
	/**
	 * 工程模板目录
	 */
	public $templateDir;
	/**
	 * 工程业务逻辑目录
	 */
	public $srcDir;
	/**
	 * 工程配置目录
	 */
	public $confDir;
	/**
	 * 工程可访问资源目录
	 */
	public $wwwDir;
	
	private $confFile;
	
	/**
	 * 创建工程接口
	 *
	 */
	public function generate() {
		WindFolder::mkRecur($this->dir);
		if (!is_writable($this->dir)) return false;
		$result = $this->generateData()
			   && $this->generateTemplate()
			   && $this->generateSrc()
			   && $this->generateConf()
			   && $this->generateWww();
		return $result;
	}
	
	/**
	 * 解析配置文件
	 *
	 * @param string $config
	 */
	public function setConfig($config) {
		if (is_string($config)) $config = @include Wind::getRealPath($config, true);
		$this->dataDir = $config['dataDir'];
		$this->templateDir = $config['templateDir'];
		$this->srcDir = $config['srcDir'];
		$this->confDir = $config['confDir'];
		$this->wwwDir = $config['wwwDir'];
	}
	
	/**
	 * 生成data目录
	 *
	 */
	protected function generateData() {
		WindFolder::mkRecur($this->dir . '/' . $this->dataDir);
		$this->dataDir = $this->_resolveDirName($this->dataDir);
		return true;
	}
	
	/**
	 * 生成模板目录
	 *
	 */
	protected function generateTemplate() {
		WindFolder::mkRecur($this->dir . '/' . $this->templateDir);
		$this->templateDir = $this->_resolveDirName($this->templateDir);
		return true;
	}
	
	/**
	 * 生成src目录及IndexController.php
	 *
	 */
	protected function generateSrc() {
		$content = <<<EOF
<?php
	
class IndexController extends WindController {
	
	public function run() {
		//TODO insert your code here
		echo 'hello, wind Framework!';
	}
	
}
?>
EOF;
		$dir = $this->dir . '/' . $this->srcDir;
		WindFolder::mkRecur($dir);
		if (!WindFile::write($dir . '/IndexController.php', $content)) return false;
		$this->srcDir = $this->_resolveDirName($this->srcDir);
		return true;
	}
	
	/**
	 * 生成conf目录及config.php
	 *
	 */
	protected function generateConf() {
		$content = <<<EOD
<?php
return array(
	'web-apps' => array(
		'%s' => array(
			'modules' => array(
				'default' => array(
					'controller-path' => '%s',
					'controller-suffix' => '%s',
					'template-dir' => '%s',
					'compile-dir' => '%s',
				)
			)
		)
	)
);
EOD;
		$alias = strtoupper($this->name) . ':';
		$content = sprintf($content,
			$this->name,
			$alias . $this->srcDir,
			'Controller',
			$alias . $this->templateDir,
			$alias . $this->dataDir . '.compile'
		);
		$dir = $this->dir . '/' . $this->confDir;
		WindFolder::mkRecur($dir);
		$this->confFile = $dir . '/config.php';
		if (!WindFile::write($this->confFile, $content)) return false;
		return true;
	}
	
	/**
	 * 生成www目录及index.php
	 *
	 */
	protected function generateWww() {
		$content = <<<EOS
<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('WIND_DEBUG', 1);
require_once '%s';
Wind::register(realpath('%s'), '%s');
Wind::application('%s', '%s')->run();
EOS;
		$dir = $this->dir . '/' . $this->wwwDir;
		WindFolder::mkRecur($dir);
		$content = sprintf($content,
			$this->_resolveRelativePath($dir, Wind::getRealPath('WIND:Wind.php', true)) ,
			$this->_resolveRelativePath($dir, $this->dir),
			strtoupper($this->name),
			$this->name,
			$this->_resolveRelativePath($dir, $this->confFile));
		if (!WindFile::write($dir . '/index.php', $content)) return false;
		return true;
	}
	
	/**
	 * 处理路径
	 *
	 * @param string $dir
	 * @return string
	 */
	private function _resolveDirName($dir) {
		return str_replace('/', '.', $dir);
	}
	
	/**
	 * 计算路径1对于路径2的相对路径
	 *
	 * @param string $sourcePath
	 * @param string $targetPath
	 * @return string
	 */
	private function _resolveRelativePath($sourcePath, $targetPath) {
		list($sourcePath, $targetPath) = array(realpath($sourcePath), realpath($targetPath));
		$src_paths = explode('/', $sourcePath);
		$tgt_paths = explode('/', $targetPath);
		$src_count = count($src_paths);
		$tgt_count = count($tgt_paths);
	
		$relative_path = '';
		//默认把不同点设在最后一个
		$break_point = $src_count;
		$i = 0;
		//计算两个路径不相同的点，然后开始往上数..
		for ($i = 0; $i < $src_count; $i++) {
			if ($src_paths[$i] == $tgt_paths[$i]) continue;
			$relative_path .= '../';
			$break_point == $src_count && $break_point = $i;
		}
		$relative_path || $relative_path = './';
	
		//往上..后，继续算目标路径的接下来的path
		for ($i = $break_point; $i < $tgt_count; $i++) {
			$relative_path .= $tgt_paths[$i] . '/';
		}
		return rtrim($relative_path, '/');
	}
}

?>