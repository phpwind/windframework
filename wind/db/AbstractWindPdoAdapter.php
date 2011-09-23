<?php
/**
 * db连接适配器抽象类定义
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-9-22
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.db
 */
abstract class AbstractWindPdoAdapter extends PDO {

	/**
	 * 过滤SQL元数据，数据库对象(如表名字，字段等)
	 *
	 * @param array $data
	 * @return string
	 */
	abstract public function fieldMeta($data);

	/**
	 * 过滤数组并组装单条 key=value 形式的SQL查询语句值(insert/update)
	 * 
	 * @param array $array
	 * @return string
	 */
	abstract public function sqlSingle($array);

	/**
	 * 过滤数组并将数组变量转换为sql字符串
	 *
	 * @param array $variable
	 * @return string
	 */
	abstract public function quoteArray($variable);

	/**
	 * 添加数据表
	 * 
	 * 添加数据表<note><b>注意:</b>最后一个参数'$replace',有两个取值'true,false',当值为false时表示如果数据表存在不创建新表,
	 * 值为true时则删除已经存在的数据表并创建新表</note>
	 * @param string $tableName 数据表名称
	 * @param string|array $values 数据表字段信息
	 * @param boolean $replace 如果表已经存在是否覆盖,接收两个值true|false
	 * @return boolean
	 */
	abstract public function createTable($tableName, $values, $replace = false);

	/**
	 * 设置连接数据库是所用的编码方式
	 *
	 * @param string $charset 编码格式
	 */
	abstract public function setCharset($charset);

}

?>