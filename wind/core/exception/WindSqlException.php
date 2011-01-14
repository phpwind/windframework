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
	

	const DB_CONN_EMPTY = 'Database configuration is empty';

	const DB_CONN_FORMAT = 'Database configuration format is incorrect';

	const DB_CONN_NOT_EXIST = 'Then identify the database connection does not exist';

	const DB_CONN_EXIST = 'Then identify the database connection is aleady exist';

	const DB_QUERY_EMPTY = 'Query is empty';

	const DB_QUERY_LINK_EMPTY = 'Query link is not validate  resource';

	const DB_QUERY_FIELD_EMPTY = 'Query field is empty';

	const DB_QUERY_FIELD_EXIST = 'Query field is not exist';

	const DB_QUERY_FIELD_FORMAT = 'Inside the field in the query not formatted correctly';

	const DB_QUERY_INSERT_DATA = 'The new data is empty';

	const DB_QUERY_UPDATE_DATA = 'To Updated data is empty';

	const DB_QUERY_CONDTTION_FORMAT = 'The conditions of query are not right';

	const DB_QUERY_GROUP_MATCH = 'Query group does not match';

	const DB_QUERY_LOGIC_MATCH = 'Query logic does not match';

	const DB_QUERY_FETCH_ERROR = 'The wrong way to obtain the result set';

	const DB_QUERY_TRAN_BEGIN = 'Transaction has not started';

	const DB_QUERY_COMPARESS_ERROR = 'Query comparison is incorrect conversion or assembly';

	const DB_QUERY_COMPARESS_EXIST = 'Comparison does not exist query';

	const DB_TABLE_EMPTY = 'Table is  empty';

	const DB_WHERE_ERROR = 'Query where is Error';

	const DB_EMPTY = 'Database is  empty';

	const DB_DRIVER_NOT_EXIST = 'The database driver does not exist';

	const DB_DRIVER_EXIST = 'The database driver is aleady exist';

	const DB_BUILDER_NOT_EXIST = 'The database builder does not exist';

	const DB_BUILDER_EXIST = 'The database builder is aleady  exist';

	const DB_ADAPTER_NOT_EXIST = 'The database adapter does not exist';

	const DB_ADAPTER_EXIST = 'The database adapter is aleady exist';

	const DB_DRIVER_BUILDER_NOT_MATCH = 'The database driver does not match with the builder';

	const DB_JOIN_TYPE_ERROR = 'The database is wrong type of join query ';

	const DB_CONNECT_NOT_EXIST = 'The database connect does not exist';
}