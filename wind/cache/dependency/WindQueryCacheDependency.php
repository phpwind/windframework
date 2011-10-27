<?php
Wind::import("WIND:cache.IWindCacheDependency");
/**
 * 数据缓存依赖类
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class WindQueryCacheDependency implements IWindCacheDependency {
	/* (non-PHPdoc)
	 * @see IWindCacheDependency::injectDependent()
	 */
	public function injectDependent($expires) {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see IWindCacheDependency::hasChanged()
	 */
	public function hasChanged($cache, $key, $expires) {
		// TODO Auto-generated method stub
		
	}

	
}

?>