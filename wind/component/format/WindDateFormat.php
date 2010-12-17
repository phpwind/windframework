<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 日期格式化
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindDateFormat{
	
	/**
	 * @var int 填充展示
	 */
	const FILL = 0;
	/**
	 * @var int 数字展示
	 */
	const DIGIT = 1;
	/**
	 * @var int 文本展示
	 */
	const TEXT = 2;
	/**
	 * @var string 默认格式化
	 */
	const DEFAULT_FORMAT = 'Y-m-d H:i:s';
	/**
	 * @var int unix时间戳
	 */
	private $time = 0;
	
	public function __construct($year=null,$month=null,$day=null,$hours=null,$minutes = null,$second = null  ){
		$this->time = mktime ($hours,$minutes,$second,$month,$day,$year);
	}

	/**
	 * 格式化输出
	 * @param int $time unix时间戳
	 * @param string $format 格式化
	 * @return string
	 */
	private static function format($time = null,$format = null) {
		return date($format ? $format : self::DEFAULT_FORMAT,$time ? $time : time());
	}
	
	 /**
	  * 判断是否是闰年
	  * @return string
	  */
	 public function isLeapYear(){
	 	return date('L',$this->time);
	 }
	
	/**
	 * 获取该月的天数
	 * @return string
	 */
	public  function daysInMonth(){
		return date('t',$this->time);
	}
	/**
	 *获取该年的天数 
	 */
	public function daysInYear(){
		return $this->isLeapYear() ? 366 : 365;
	}
	/**
	 * 所表示的日期是该年中的第几天。
	 */
	public function dayOfYear(){
		return date('z',$this->time)+1;
	}
	
	/**
	 *表示的日期为该月中的第几天。 
	 */
	public function dayOfMonth(){
		return date('j',$this->time);
	}
	
	/**
	 *表示的日期是该星期中的第几天。
	 */
	public function dayOfWeek(){
		return date('w',$this->time)+1;
	}
	/**
	 *判断日期所在年的第几周 
	 */
	public function WeekOfYear(){
		return date('W',$this->time);
	}
	
	/**
	 * 获取年份
	 * @param boolean $flag
	 * @return string
	 */
	public function getYear($flag = true){
		return $flag ? date('Y',$this->time) : date('y',$this->time);
	}
	
	/**
	 * 获取月份
	 * @param int $display 显示类型
	 * @return string
	 */
	public function getMonth($display = WindDateTime::FILL){
		$format  = 'n';
		switch($display){
			case WindDateTime::FILL:$format ='m';break;
			case WindDateTime::DIGI:$format ='n';break;
			case WindDateTime::TEXT:$format ='M';break;
			default:$format ='n'; break;
			
		}
		return date($format,$this->time);
	}
	
	/**
	 * 获取天数
	 * @param string $display 显示类型
	 * @return string
	 */
	public function getDay($display = WindDateTime::FILL){
		$format  = 'j';
		switch($display){
			case WindDateTime::FILL:$format ='d';break;
			case WindDateTime::DIGI:$format ='j';break;
			case WindDateTime::TEXT:$format ='D';break;
			default:$format ='j'; break;
			
		}
		return date($format,$this->time);
	}
	
	/**
	 * 获取星期
	 * @param string $display 显示类型
	 * @return string
	 */
	public function getWeek($display = WindDateTime::FILL){
		$format  = 'jS';
		switch($display){
			case WindDateTime::FILL:$format ='jS';break;
			case WindDateTime::DIGI:$format ='w';break;
			case WindDateTime::TEXT:$format ='S';break;
			default:$format ='N'; break;
			
		}
		return date($format,$this->time);
	}
	
	/**
	 * 获取小时
	 * @param string $display 显示类型
	 * @param boolean $type 是否是24小时制
	 * @return string
	 */
	public function getHours($display = WindDateTime::FILL,$type = true){
		$format  = 'H';
		switch($display){
			case WindDateTime::FILL:$format = $type ? 'H' : 'h';break;
			case WindDateTime::DIGI:$format =$type ? 'G' : 'g';break;
			default:$format ='H'; break;
			
		}
		return date($format,$this->time);
	}
	
	/**
	 * 获取星期
	 * @return string
	 */
	public function getMinutes(){
		return date('i',$this->time);
	}
	
	/**
	 * 获取秒数
	 * @return string
	 */
	public function getSeconds(){
		return date('s',$this->time);
	}
	
	/**
	 * 获取时间
	 * @return string
	 */
	public function getTimeZone(){
		return function_exists('date_default_timezone_get') ? date_default_timezone_get(): date('e',$this->time);
	}
	
	/**
	 * 获取本地时区
	 * @return string
	 */
	public function getLocalTimeZone(){
		return date('T',$this->time);
	}
	
	/**
	 * 取得unix时间戳
	 * @return string
	 */
	public function getUnixTimeStamp(){
		return date('U',$this->time);
	}
	
	/**
	 * 重新设置时间
	 * @param mixed $time
	 */
	public function setTime($time){
		if(is_string($time) || is_int($time)){
			$this->time = is_string($time) ? strtotime($time) : $time;
		}
	}
	
	/**
	 * 取得当前时间
	 * @return WindDateFormat
	 */
	public function getNow(){
		$date = getdate(time());
		return new self($date["year"],$date["mon"],$date["mday"],$date["hours"],$date["minutes"],$date["seconds"]);
	}
	
	/**
	 * 设置默认时区
	 * @param string $timezone 时间
	 */
	public static function setTimezone($timezone){
		function_exists('date_default_timezone_set') ? date_default_timezone_set ($timezone) : putenv("TZ={$timezone}");
	}
	
	/**
	 * 获取日期的某部分
	 * @param string $interval 字符串表达式 ,时间间隔类型
	 * @param mixed $date 表示日期的文字
	 * @return string 返回日期的某部分
	 */
	public static function datePart($interval,$date){
		return date($interval,is_string($date) ? strtotime($date) : $date);
	}
	
	/**
	 * 获取两个日期的差
	 * @param string $interval 返回两个日期差的间隔类型
	 * @param mixed $startDate 开始日期
	 * @param mixed $endDate   结束日期
	 * @return number
	 */
	public static function dateDiff($interval,$startDate,$endDate){
		$startDate = is_string($startDate) ?  strtotime($startDate) : $startDate;
		$endDate = is_string($endDate) ?  strtotime($endDate) : $endDate;
		$diff = $endDate - $startDate;
		$retval = 0;
		switch($interval)
		{
			case "y": $retval = bcdiv($diff, (60 * 60 * 24 * 365)); break;
			case "m": $retval = bcdiv($diff, (60 * 60 * 24 * 30)); break;
			case "w": $retval = bcdiv($diff, (60 * 60 * 24 * 7)); break;
			case "d": $retval = bcdiv($diff, (60 * 60 * 24)); break;
			case "h": $retval = bcdiv($diff, (60 * 60)); break;
			case "n": $retval = bcdiv($diff, 60); break;
			case "s": $retval = $diff; break;
		}
		return $retval;
	}
	
	/**
	 * 返回向指定日期追加指定间隔类型的一段时间间隔后的日期  
	 * @param string $interval 字符串表达式，是所要加上去的时间间隔类型。
	 * @param int $value 数值表达式，是要加上的时间间隔的数目。其数值可以为正数（得到未来的日期），也可以为负数（得到过去的日期）。 
	 * @param string $date 表示日期的文字，这一日期还加上了时间间隔。 
	 * @param mixed $format 格式化输出
	 * @return string 返回追加后的时间间隔
	 */
	public static function dateAdd($interval,$value,$date,$format = null){
		$date = getdate(is_string($date) ? strtotime($date) : $date);
		switch($interval)
		{
			case "y": $date["year"] += $value; break;
			case "q": $date["mon"] += ($value * 3); break;
			case "m": $date["mon"] += $value; break;
			case "w": $date["mday"] += ($value * 7); break;
			case "d": $date["mday"] += $value; break;
			case "h": $date["hours"] += $value; break;
			case "n": $date["minutes"] += $value; break;
			case "s": $date["seconds"] += $value; break;
		}
		return date($format ? $format : self::DEFAULT_FORMAT, mktime($date["hours"], $date["minutes"], $date["seconds"], $date["mon"], $date["mday"], $date["year"]));
	}
	
	/**
	 * 得到一年中每个月真实的天数
	 * @param string $year
	 * @return array
	 */
	public static function sGetRealDaysInMonthsOfYear($year){
		$months = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if($this->isLeapYear($year)){
			$months[1] = 29;
		}
		return $months;
	}
	
	/**
	 * 判断是否是闰年
	 * @param int $year
	 * @return string
	 */
	public static function sIsLeapYear ($year){
	  		if(!is_int($year) || $year < 0 || 0 !=($year % 4) || 4 != strlen($year)){
	  			return false;
	  		}
	        if (0 == $year % 400) {
	            return true;
	        } else if (1582 < $year  && 0 == ($year % 100)) {
	            return false;
	        }
	        return true;
	  }
	
	/**
	 * 获取该月的天数
	 * @param int $month 月份
	 * @param int $year 年份
	 * return int
	 */
	public static function sDaysInMonth($month,$year){
		if ( 1 > $month  || 12 < $month){
			return 0;
		}
		if(!($daysInmonths = $this->getRealDaysInMonthsOfYear($year))){
			return 0;
		}
		return $daysInmonths[$month - 1];
	}
	
	/**
	 *获取该年的天数 
	 */
	public static function sDaysInYear($year){
		return self::sIsLeapYear($year) ? '366' : '365';
	}
	
	public function __toString(){
		$this->toString();
	}
	
	public function toString($format = null){
		return date($format ? $format : self::DEFAULT_FORMAT,$this->time);
	}
	
	/**
	 * 获取UTc日期格式
	 * @return string
	 */
	public function toUTCString(){
		$oldTimezone = $this->getTimezone();
		if('UTC' !== strtoupper($oldTimezone)){
			self::setTimezone('UTC');
		}
		$date = $this->toString('D, d M y H:i:s e');
		if('UTC' !== strtoupper($oldTimezone)){
			self::setTimezone($oldTimezone);
		}
		return $date;
	}
}
	
	