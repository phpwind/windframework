<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMemcache implements IWindCache{
	
	public function add($key, $value, $expires = null, IWindCacheDependency $denpendency = null, $tags = array()) {
		
	}
	public function set($key, $value, $expires = null, IWindCacheDependency $denpendency = null, $tags = array()) {
		
	}
	public function replace($key, $value, $expires = null, IWindCacheDependency $denpendency = null, $tags = array()) {
		
	}
	public function fetch($key, $tags = array()) {
		
	}
	public function fetchByTags($tags) {
		
	}
	public function batchFetch($keys) {
		
	}
	public function delete($key, $tags = array()) {
		
	}

	public function deteteByTags($tags) {
		
	}

	public function batchDelete($keys) {
		
	}

	public function flush() {
		
	}
	
}