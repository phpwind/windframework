<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * phpwind模板编译引擎
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WTemplate {
	/**
	 * 获得模板信息
	 * @param string $from 模板存放位置
	 * @param string $to 编译好的模板位置
	 * @param array $vars 传入模板的变量
	 * @param integer $time 模板的有效时间
	 * @return string 输出的内容
	 */ 
	public function fetch($from, $to, $vars, $time = null) {
		if (!$this->checkCache($from, $to, $time))  $this->compile($from, $to);
		extract($vars, EXTR_OVERWRITE );
		include $to;
		return $this->filterOutPut(ob_get_contents());
	}
	/**
	 * 过滤输出的数据
	 * @param string $_output
	 * @return string $_output
	 */
	//TODO 此处可以添加额外的filter
	private function filterOutPut($_output) {
		//$_output = str_replace(array("\r", '<!--<!---->-->', '<!---->-->', '<!--<!---->', "<!---->\n", '<!---->', '<!-- -->', "<!--\n-->", "\t\t", '    ', "\n\t", "\n\n"), array('', '', '', '', '', '', '', '', '', '',"\n", "\n"), $_output);
		$_output = str_replace(array('<!--<!---->-->','<!---->-->', '<!--<!---->', "<!---->\r\n", '<!---->', '<!-- -->', "\t\t\t"), '', $_output);
		return $_output;
	}
	/**
	 * 编译模板生成模板缓存文件
	 * @param string templateFile
	 * @param string $cacheFile
	 */
	private function compile($templateFile, $cacheFile) {
		include (R_P . '/all_lang.php');		//模板内的文字
		$content = preg_replace("/{#([\w]+?)}/eis",'$lang[\\1]', readover($templateFile));
		$this->createLangForder($cacheFile);
		if (readover($cacheFile) != $content) {
			writeover($cacheFile, $content);
		}
	}
	private function createLangForder($file) {
		$to_dir = substr($file, 0, strrpos($file,'/'));
		if (!is_dir($to_dir)) {
			$this->createFile(dirname($to_dir));
			@mkdir($to_dir);
			@chmod($to_dir,0777);
			@fclose(@fopen($to_dir.'/index.html','w'));
			@chmod($to_dir.'/index.html',0777);
		}
	}
	private function createFile($path) {
		if (!is_dir($path)) {
			$this->createFile(dirname($path));
			@mkdir($path);
			@chmod($path,0777);
		}
	}
	/**
	 * 判断是否已经编译
	 * @param string $template
	 * @param string $cache
	 * @param integer $time 缓存更新时间
	 */
	private function checkCache($template, $cache, $time = null) {
		if (!file_exists($cache)) return false; 
		if (filemtime($cache) < filemtime($template)) return false;//检查模板是否更新
		if ($time && time() > (filemtime($cache)+intval($time))) return false;
		return true;
	}
}