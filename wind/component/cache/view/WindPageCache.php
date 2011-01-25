<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 页面缓存
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindPageCache {
	/**
	 * @var string 静态化的目录
	 */
	public $htmCacheDir = './htm';
	/**
	 * @var string 模板目录
	 */
	public $tplDir = './tpl';
	/**
	 * @var string 模板变量的左定界符
	 */
	public $lDelimiter = '{';
	/**
	 * @var string 模板变量的右定界符
	 */
	public $rDelimiter = '}';
	/**
	 * 静态文件后缀
	 * @var string
	 */
	public $staticSuffix = 'htm';
	/**
	 * @var string 模板内容
	 */
	protected $content = '';
	/**
	 * 加载模板文件
	 * @param string $tplname
	 * @return boolean
	 */
	public function loadTpl($tplname) {
		if (!in_array($this->getFileSuffix($tplname), array('htm', 'html', 'shtml'))) {
			return false;
		}
		$path = rtrim($this->tplDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $tplname;
		if (false === is_file($path)) {
			return false;
		}
		ob_start();
		require $path;
		$this->content = ob_get_contents();
		ob_end_clean();
		return true;
	}
	/**
	 * 替换模板变量
	 * @param string $tplvar
	 * @param string $value
	 * @return string|string
	 */
	public function assign($tplvar, $value) {
		if ('' === $this->content) {
			return false;
		}
		$this->content = str_replace($this->lDelimiter . $tplvar . $this->rDelimiter, $value, $this->content);
		return true;
	}
	/**
	 * 存储静态化的文件
	 * @param string $htmFileName 静态化页面文件
	 * @return boolean
	 */
	public function pageStatic($htmFileName) {
		if (!in_array($this->staticSuffix, array('htm', 'html', 'shtml'))) {
			return false;
		}
		$path = rtrim($this->htmCacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		if (false === is_dir($path)) {
			mkdir($path, 0777, true);
		}
		$realpath = $path . $htmFileName . '.' . $this->staticSuffix;
		if (is_file($realpath)) {
			return false;
		}
		file_put_contents($realpath, $this->content);
		return true;
	
	}
	/**
	 * 取得模板内容
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
	/**
	 * 清理静态缓存文件
	 * @param int $expires 过期时间 ,单位为秒
	 */
	public function clearCache($expires = 0) {
		if (false === is_dir($this->htmCacheDir)) {
			return false;
		}
		$path = rtrim($this->htmCacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$hd = dir($this->htmCacheDir);
		while (false != ($entry = $hd->read())) {
			$realpath = $path . $entry;
			if (is_file($realpath) && (($expires && filectime($realpath) <= time() - $expires) || !$expires)) {
				unlink($realpath);
			}
		}
		$hd->close();
		return true;
	
	}
	/**
	 * 获取文件后缀
	 * @param string $filename
	 * @return string
	 */
	private function getFileSuffix($filename) {
		return substr($filename, strrpos($filename, '.') + 1);
	}

}