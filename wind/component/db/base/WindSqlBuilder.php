<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindSqlBuilder {
	
	/**
	 * @var array 运算表达式
	 */
	protected $compare = array ('gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'eq' => '=', 'neq' => '!=', 'in' => 'IN', 'notin' => 'NOT IN', 'notlike' => 'NOT LIKE', 'like' => 'LIKE' );
	/**
	 * @var array 逻辑运算符
	 */
	protected $logic = array ('and' => 'AND', 'or' => 'OR', 'xor' => 'XOR' );
	/**
	 * @var array 分组条件
	 */
	protected $group = array ('lg' => '(', 'rg' => ')' );
	
	/**
	 * 表解析
	 * @example array('tablename'=>'alais') or array('tablename'),tablename as alais
	 * @param array|string $table 表
	 * @return string;
	 */
	public abstract function buildTable($table = array());
	/**
	 * 是否查找相同的列
	 * @param boolean $distinct
	 * @return string
	 */
	public abstract function buildDistinct($distinct = false);
	/**
	 * 解析表的列名
   	 * @example array('filedname'=>'alais') or array('filedname'),filedname as alais
	 * @param array|string $field 查询的字段
	 * @return string
	 */
	public abstract function buildField($field = array());
	/**
	 * 解析连接查询
	 * @example array('tablename'=>array(jointype,onwhere,alias)) or array('left join tablename as a on a.id=b.id') 
	 * 			'left join tablename as a on a.id=b.id'
	 * @param string|array $join 连接条件
	 * @return string
	 */
	public abstract function buildJoin($join = array());
	/**
	 * 解析查询条件
	 * @example array('lg','gt'=>('age',2),and,'lt'=>array('age',23),'gt',or,like=>array('name','suqian%')) or 
	 * 			( age > 2 and age < 23) or name like 'suqian%'
	 * @param array $where 查询条件
	 * @return string
	 */
	public abstract function buildWhere($where = array());
	/**
	 * 解析分组
	 * @example array('field1','field2') or 'group by field1,field2'
	 * @param string|array $group 分组条件
	 * @return string
	 */
	public abstract function buildGroup($group = array());
	/**
	 * 解析排序
	 * @example array('field1'=>'desc','field2'=>'asc') or 'order by field1 desc,field2 asc'
	 * @param array|string $order 排序条件
	 * @return string
	 */
	public abstract function buildOrder($order = array());
	/**
	 * 解析对分组的过滤语句
	 * @param string $having
	 * @return string
	 */
	public abstract function buildHaving($having = '');
	/**
	 * 解析查询limit语句
	 * @param int $limit  取得条数
	 * @param int $offset 偏移量
	 * @return string
	 */
	public abstract function buildLimit($limit = 0, $offset = 0);
	/**
	 * 解析更新数据
	 * @example array('field'=>'value');
	 * @param array $data 
	 * @return string
	 */
	public abstract function buildSet($data);
	/**
	 * 解析添加数据
	 * @example array('field1','field2') or array(array('field1','field2'),array('field1','field2'))
	 * @param array $setData
	 * @return string
	 */
	public abstract function buildData($setData);
	
	/**
	 *返回影响行数的sql语句
	 *@param $ifquery 是否是select 语句
	 *@return string 
	 */
	public abstract function buildAffected($ifquery);
	
	/**
	 *返回取得最后新增的sql语句
	 *@return string 
	 */
	public abstract function buildLastInsertId();
	
	/**
	 * 对字符串转义
	 * @param string $value
	 * @return string
	 */
	public abstract function escapeString($value);
	
	/**
	 * @param strint $schema 数据库名
	 */
	public abstract function getMetaTableSql($schema);
	
	/**
	 * @param string $table  表名
	 */
	public abstract function getMetaColumnSql($table);
	/**
	 * 解析新增SQL语句
	 * @param array $option
	 * @return string
	 */
	public function getInsertSql($option) {
		return sprintf ( "INSERT%s%sVALUES%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	/**
	 * 解析更新QL语句
	 * @param array $option
	 * @return string
	 */
	public function getUpdateSql($option) {
		return sprintf ( "UPDATE%sSET%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildSet ( $option ['set'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * 解析删除SQL语句
	 * @param array $option
	 * @return string
	 */
	public function getDeleteSql($option) {
		return sprintf ( "DELETE FROM%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * 解析查询SQL语句
	 * @param array $option
	 * @return string
	 */
	public function getSelectSql($option) {
		return sprintf ( "SELECT%s%sFROM%s%s%s%s%s%s%s", $this->buildDistinct ( $option ['distinct'] ), $this->buildField ( $option ['field'] ), $this->buildTable ( $option ['table'] ), $this->buildJoin ($option ['join']), $this->buildWhere ( $option ['where'] ), $this->buildGroup ( $option ['group'] ), $this->buildHaving ( $option ['having'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'], $option ['offset'] ) );
	}
	
	/**
	 * 解析replace SQL语句
	 * @param array $option
	 * @return string
	 */
	public function getReplaceSql($option){
		return sprintf ( "REPLACE%s%sSET%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	
	public function getAffectedSql($ifquery){
		return sprintf ("SELECT%s",$this->buildAffected($ifquery));
	}
	
	public function getLastInsertIdSql(){
		return sprintf ("SELECT%s",$this->buildLastInsertId());
	}
	
	/**
	 * 判断是否是二维数组
	 * @param array $array
	 * @return number
	 */
	public function getDimension($array = array()) {
		$dim = 0;
		foreach ($array as $value ) {
			return  is_array($value) ? $dim+=2 : ++$dim;
		}
		return $dim;
	}
	
	/**
	 * 要解析的一维数组，单条添加数据
	 * @param array $data 要解析的数据
	 * @return string
	 */
	public function buildSingleData($data) {
		foreach ( $data as $key => $value ) {
			$data [$key] = $this->escapeString ( $value );
		}
		return $this->sqlFillSpace('(' . implode ( ',', $data ) . ')');
	}
	
	/**
	 * 解析二维数组，批量添加
	 * @param array $multiData 要解析的数据
	 * @return string
	 */
	public function buildMultiData($multiData) {
		$iValue = '';
		foreach ( $multiData as $data ) {
			$iValue .= $this->buildSingleData ( $data );
		}
		return $iValue;
	}
	
	/**
	 * 在字符串头尾添加空格或空白字符
	 * @param string $value  字符串
	 * @return string
	 */
	public function sqlFillSpace($value) {
		return str_pad ( $value, strlen ( $value ) + 2, " ", STR_PAD_BOTH );
	}
}