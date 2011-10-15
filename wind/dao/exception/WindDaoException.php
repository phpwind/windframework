<?php
Wind::import('WIND:exception.WindException');
/**
 * Dao异常处理类
 * 
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package dao
 * @subpackage exception
 */
class WindDaoException extends WindException {

	/* (non-PHPdoc)
	 * @see WindException::messageMapper()
	 */
	protected function messageMapper($code) {
		$messages = array();
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}
}

?>