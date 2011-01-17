<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.exception.WindException');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSqlException extends WindException {
	//TODO change exception message like WindException.
	const DB_CONN_EMPTY = 200;

	const DB_CONN_FORMAT = 201;

	const DB_CONN_NOT_EXIST = 202;

	const DB_CONN_EXIST = 203;

	const DB_CONNECT_NOT_EXIST = 204;

	const DB_QUERY_EMPTY = 210;

	const DB_QUERY_LINK_EMPTY = 211;

	const DB_QUERY_FIELD_EMPTY = 212;

	const DB_QUERY_FIELD_EXIST = 213;

	const DB_QUERY_FIELD_FORMAT = 214;

	const DB_QUERY_INSERT_DATA = 215;

	const DB_QUERY_UPDATE_DATA = 216;

	const DB_QUERY_CONDTTION_FORMAT = 217;

	const DB_QUERY_GROUP_MATCH = 218;

	const DB_QUERY_LOGIC_MATCH = 219;

	const DB_QUERY_FETCH_ERROR = 220;

	const DB_QUERY_TRAN_BEGIN = 221;

	const DB_QUERY_COMPARESS_ERROR = 222;

	const DB_QUERY_COMPARESS_EXIST = 223;

	const DB_QUERY_WHERE_ERROR = 224;

	const DB_QUERY_JOIN_TYPE_ERROR = 225;

	const DB_TABLE_EMPTY = 240;

	const DB_EMPTY = 241;

	const DB_DRIVER_NOT_EXIST = 242;

	const DB_DRIVER_EXIST = 243;

	const DB_BUILDER_NOT_EXIST = 250;

	const DB_BUILDER_EXIST = 251;
	
	const DB_DRIVER_BUILDER_NOT_MATCH = 252;

	const DB_ADAPTER_NOT_EXIST = 260;

	const DB_ADAPTER_EXIST = 261;
	
	/**
	 * 重定义异常类型
	 * 
	 * @see WindException::messageMapper()
	 * @param int $code 异常号
	 * @return string   最终输出异常信息的原型
	 */
	protected function messageMapper($code) {
		$messages = array(
			self::DB_CONN_EMPTY => '\'$message\' Database configuration is empty.', 
			self::DB_CONN_FORMAT => ' \'$message\' Database configuration format is incorrect.',
			self::DB_CONN_NOT_EXIST => '\'$message\' Then identify the database connection does not exist.', 
			self::DB_CONN_EXIST => '\'$message\' Then identify the database connection is aleady exist.', 
			self::DB_CONNECT_NOT_EXIST => '\'$message\' The database connect does not exist.', 
			self::DB_QUERY_EMPTY => '\'$message\' Query is empty.', 
			self::DB_QUERY_LINK_EMPTY => '\'$message\' Query link is not validate  resource.', 
			self::DB_QUERY_FIELD_EMPTY => '\'$message\' Query field is empty.', 
			self::DB_QUERY_FIELD_EXIST => '\'$message\' Query field is not exist.', 
			self::DB_QUERY_FIELD_FORMAT => '\'$message\' Inside the field in the query not formatted correctly.', 
			self::DB_QUERY_INSERT_DATA => '\'$message\' The new data is empty.', 
			self::DB_QUERY_UPDATE_DATA => '\'$message\' To Updated data is empty.', 
			self::DB_QUERY_CONDTTION_FORMAT => '\'$message\' The conditions of query are not right.', 
			self::DB_QUERY_GROUP_MATCH => '\'$message\' Query group does not match.', 
			self::DB_QUERY_LOGIC_MATCH => '\'$message\' Query logic does not match.', 
			self::DB_QUERY_FETCH_ERROR => '\'$message\' The wrong way to obtain the result set.', 
			self::DB_QUERY_TRAN_BEGIN => '\'$message\' Transaction has not started.', 
			self::DB_QUERY_COMPARESS_ERROR => '\'$message\' Query comparison is incorrect conversion or assembly.', 
			self::DB_QUERY_COMPARESS_EXIST => '\'$message\' Comparison does not exist query.', 
			self::DB_QUERY_WHERE_ERROR => '\'$message\' Query where is Error.', 
			self::DB_QUERY_JOIN_TYPE_ERROR => '\'$message\' The database is wrong type of join query.', 
			self::DB_TABLE_EMPTY => '\'$message\' Table is empty.', 
			self::DB_EMPTY => '\'$message\' Database is empty.', 
			self::DB_DRIVER_NOT_EXIST => '\'$message\' The database driver does not exist.', 
			self::DB_DRIVER_EXIST => '\'$message\' The database driver is aleady exist.', 
			self::DB_BUILDER_NOT_EXIST => '\'$message\' The database builder does not exist.', 
			self::DB_BUILDER_EXIST => '\'$message\' The database builder is aleady  exist.', 
			self::DB_ADAPTER_NOT_EXIST => '\'$message\' The database adapter does not exist.', 
			self::DB_ADAPTER_EXIST => '\'$message\' The database adapter is aleady exist.', 
			self::DB_DRIVER_BUILDER_NOT_MATCH => '\'$message\' The database driver does not match with the builder.', 
		);
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}
}