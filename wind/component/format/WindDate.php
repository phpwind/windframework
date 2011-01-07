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
class WindDate {
	
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
	
	/**
	 * 根据输入的日期格式转化为时间戳进行属性time初始化
	 * 
	 * mktime函数，在只有输入一个年份的时候，就会默认转化为上一年的最后一天，输入一个月份并且缺省输入day的时候，
	 * 会转化为上个月的最后一天。所以这种情况需要注意。
	 * 例如: date('Y-m-d', mktime(0, 0, 0, 0, 0, 2010)) == 2009-11-30，而非2010-1-1
	 * 例如：date('Y-m-d', mktime(0, 0, 0, 1, 0, 2010)) == 2009-12-31， 而非2010-1-1
	 * 例如：date('Y-m-d', mktime(0, 0, 0, 2, 0, 2010)) == 2010-1-30, 而非2010-2-1
	 * 例如：date('Y-m-d', mktime(2, 0, 0, 0, 1, 2010)) == 2009-12-01, 而非2010-1-1
	 * 如果该构造函数没有参数传入的时候，得到的日期不是期望的当前日期，而是上两年的11月的30日
	 * 
	 * 如果月份为空：
	 *     如果年份为空，则取当前月份；否则取1
	 * 如果日期为空：
	 *     如果年份为空，则取当前日期，否则取1
	 * 如果小时为空：
	 *     如果年份为空，则取当前小时
	 * 如果分为空：
	 *     如果年份为空，则取当前分
	 * 如果秒为空：
	 *     如果年份为空，则取当前秒
	 * 如果年份为空：
	 *     取当前年份
	 * 
	 * @param int $year     年
	 * @param int $month    月
	 * @param int $day      日
	 * @param int $hours    小时
	 * @param int $minutes  分
	 * @param int $second   秒
	 */
	public function __construct($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		!$month && ((!$year) ? $month = date('m', time()) : $month = 1);
		!$day && ((!$year) ? $day = date('d', time()) : $day = 1);
		!$hours && !$year && $hours = date('H', time());
		!$minutes && !$year && $minutes = date('i', time());
		!$second && !$year && $second = date('s', time());
		!$year && $year = date('Y', time());
		$this->time = mktime($hours, $minutes, $second, $month, $day, $year);
	}
	
	/**
	 * 判断是否是闰年
	 * 
	 * @return string  返回1或是0
	 */
	public function isLeapYear() {
		return date('L', $this->time);
	}
	
	/**
	 * 获取该月的天数
	 * 
	 * @return string 
	 */
	public function daysInMonth() {
		return date('t', $this->time);
	}
	
	/**
	 * 获取该年的天数 
	 * 
	 * @return int 如果是闰年返回366否则返回365
	 */
	public function daysInYear() {
		return $this->isLeapYear() ? 366 : 365;
	}
	
	/**
	 * 所表示的日期是该年中的第几天。
	 * 
	 * @return int 返回时该年中的第几天
	 */
	public function dayOfYear() {
		return date('z', $this->time) + 1;
	}
	
	/**
	 * 表示的日期为该月中的第几天。 
	 * 
	 * @return int 
	 */
	public function dayOfMonth() {
		return date('j', $this->time);
	}
	
	/**
	 * 表示的日期是该星期中的第几天。
	 * 
	 * @return int 
	 */
	public function dayOfWeek() {
		return date('w', $this->time) + 1;
	}
	
	/**
	 * 判断日期所在年的第几周 
	 * 
	 * @return int
	 */
	public function WeekOfYear() {
		return date('W', $this->time);
	}
	
	/**
	 * 获取年份
	 * 
	 * @param boolean $flag 是否返回四位格式的年份或是两位格式的年份
	 * @return string
	 */
	public function getYear($flag = true) {
		return $flag ? date('Y', $this->time) : date('y', $this->time);
	}
	
	/**
	 * 获取月份
	 * 
	 * @param int $display 显示类型
	 * @return string
	 */
	public function getMonth($display = self::FILL) {
		$format = 'n';
		switch ($display) {
			case self::FILL:
				$format = 'm';
				break;
			case self::DIGIT:
				$format = 'n';
				break;
			case self::TEXT:
				$format = 'M';
				break;
			default:
				$format = 'n';
				break;
		
		}
		return date($format, $this->time);
	}
	
	/**
	 * 获取天数
	 * 
	 * @param string $display 显示类型
	 * @return string 
	 */
	public function getDay($display = self::FILL) {
		$format = 'j';
		switch ($display) {
			case self::FILL:
				$format = 'd';
				break;
			case self::DIGIT:
				$format = 'j';
				break;
			case self::TEXT:
				$format = 'D';
				break;
			default:
				$format = 'j';
				break;
		
		}
		return date($format, $this->time);
	}
	
	/**
	 * 获取星期
	 * 
	 * @param string $display 显示类型
	 * @return string
	 */
	public function getWeek($display = self::FILL) {
		$format = 'jS';
		switch ($display) {
			case self::FILL:
				$format = 'jS';
				break;
			case self::DIGIT:
				$format = 'w';
				break;
			case self::TEXT:
				$format = 'S';
				break;
			default:
				$format = 'N';
				break;
		
		}
		return date($format, $this->time);
	}
	
	/**
	 * 获取小时
	 * 
	 * @param string $display 显示类型
	 * @param boolean $type 是否是24小时制
	 * @return string
	 */
	public function getHours($display = self::FILL, $type = true) {
		$format = 'H';
		switch ($display) {
			case self::FILL:
				$format = $type ? 'H' : 'h';
				break;
			case self::DIGIT:
				$format = $type ? 'G' : 'g';
				break;
			default:
				$format = 'H';
				break;
		
		}
		return date($format, $this->time);
	}
	
	/**
	 * 获取分
	 * 
	 * @return string
	 */
	public function getMinutes() {
		return date('i', $this->time);
	}
	
	/**
	 * 获取秒数
	 * 
	 * @return string
	 */
	public function getSeconds() {
		return date('s', $this->time);
	}
	
	/**
	 * 获取本地时区
	 * 
	 * @return string
	 */
	public function getLocalTimeZone() {
		return date('T', $this->time);
	}
	
	/**
	 * 取得unix时间戳
	 * 
	 * @return string
	 */
	public function getUnixTimeStamp() {
		return date('U', $this->time);
	}
	
	/**
	 * 重新设置时间
	 * 
	 * @param string | int  $time  
	 */
	public function setTime($time) {
		if ((is_string($time)&& ($time = strtotime($time))) || is_int($time)) {
			$this->time = $time;
		}
	}
	
	/**
	 * 取得当前时间
	 * 
	 * @return WindDate
	 */
	public function getNow() {
		$date = getdate(time());
		return new self($date["year"], $date["mon"], $date["mday"], $date["hours"], $date["minutes"], $date["seconds"]);
	}
	
	/**
	 * 对象转化为字符串，魔术方法
	 * 
	 * @return string 
	 */
	public function __toString() {
		return $this->toString();
	}
	
	/**
	 * 格式化时间输出
	 * 
	 * @param string $format 需要输出的格式
	 * @return string 
	 */
	public function toString($format = null) {
		return date($format ? $format : self::DEFAULT_FORMAT, $this->time);
	}
	
	/**
	 * 获取UTC日期格式
	 * 
	 * @return string
	 */
	public function toUTCString() {
		$oldTimezone = self::getTimezone();
		if ('UTC' !== strtoupper($oldTimezone)) {
			self::setTimezone('UTC');
		}
		$date = $this->toString('D, d M y H:i:s e');
		if ('UTC' !== strtoupper($oldTimezone)) {
			self::setTimezone($oldTimezone);
		}
		return $date;
	}
	
	/**
	 * 获取时区
	 * 
	 * @return string
	 */
	public static function getTimeZone() {
		return function_exists('date_default_timezone_get') ? date_default_timezone_get() : date('e');
	}
	
	/**
	 * 设置时区
	 * 
	 * @param string $timezone 时区
	 */
	public static function setTimezone($timezone) {
		function_exists('date_default_timezone_set') ? date_default_timezone_set($timezone) : putenv("TZ={$timezone}");
	}
	
	/**
	 * 格式化输出
	 * 
	 * @param string $format 格式化
	 * @param int $time unix时间戳
	 * @return string
	 */
	public static function format($format = null, $time = null) {
		return date($format ? $format : self::DEFAULT_FORMAT, $time ? $time : time());
	}
	
	/**
	 * 获取日期的某部分
	 * 
	 * @param string $interval 字符串表达式 ,时间间隔类型
	 * @param mixed $date 表示日期的文字
	 * @return string 返回日期的某部分
	 */
	public static function datePart($interval, $date) {
		return date($interval, is_string($date) ? strtotime($date) : $date);
	}
	
	/**
	 * 获取两个日期的差
	 * 
	 * @param string $interval 返回两个日期差的间隔类型
	 * @param mixed $startDate 开始日期
	 * @param mixed $endDate   结束日期
	 * @return string 
	 */
	public static function dateDiff($interval, $startDate, $endDate) {
		$startDate = is_string($startDate) ? strtotime($startDate) : $startDate;
		$endDate = is_string($endDate) ? strtotime($endDate) : $endDate;
		$diff = $endDate - $startDate;
		$retval = 0;
		switch ($interval) {
			case "y":
				$retval = bcdiv($diff, (60 * 60 * 24 * 365));
				break;
			case "m":
				$retval = bcdiv($diff, (60 * 60 * 24 * 30));
				break;
			case "w":
				$retval = bcdiv($diff, (60 * 60 * 24 * 7));
				break;
			case "d":
				$retval = bcdiv($diff, (60 * 60 * 24));
				break;
			case "h":
				$retval = bcdiv($diff, (60 * 60));
				break;
			case "n":
				$retval = bcdiv($diff, 60);
				break;
			case "s":
			default:
				$retval = $diff;
				break;
		}
		return $retval;
	}
	
	/**
	 * 返回向指定日期追加指定间隔类型的一段时间间隔后的日期  
	 * 
	 * @param string $interval 字符串表达式，是所要加上去的时间间隔类型。
	 * @param int $value 数值表达式，是要加上的时间间隔的数目。其数值可以为正数（得到未来的日期），也可以为负数（得到过去的日期）。 
	 * @param string $date 表示日期的文字，这一日期还加上了时间间隔。 
	 * @param mixed $format 格式化输出
	 * @return string 返回追加后的时间
	 */
	public static function dateAdd($interval, $value, $date, $format = null) {
		$date = getdate(is_string($date) ? strtotime($date) : $date);
		switch ($interval) {
			case "y":
				$date["year"] += $value;
				break;
			case "q":
				$date["mon"] += ($value * 3);
				break;
			case "m":
				$date["mon"] += $value;
				break;
			case "w":
				$date["mday"] += ($value * 7);
				break;
			case "d":
				$date["mday"] += $value;
				break;
			case "h":
				$date["hours"] += $value;
				break;
			case "n":
				$date["minutes"] += $value;
				break;
			case "s":
			default:
				$date["seconds"] += $value;
				break;
		}
		return self::format($format, mktime($date["hours"], $date["minutes"], $date["seconds"], $date["mon"], $date["mday"], $date["year"]));
	}
	
	/**
	 * 得到一年中每个月真实的天数
	 * 
	 * @param string $year 需要获得的月份天数的年份
	 * @return array 每月的天数组成的数组
	 */
	public static function sGetRealDaysInMonthsOfYear($year) {
		$months = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if (self::sIsLeapYear($year)) {
			$months[1] = 29;
		}
		return $months;
	}
	
	/**
	 * 判断是否是闰年
	 * 
	 * @param int $year
	 * @return string
	 */
	public static function sIsLeapYear($year) {
		if ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * 获取该月的天数
	 * 
	 * @param int $month 月份
	 * @param int $year 年份
	 * @return int
	 */
	public static function sDaysInMonth($month, $year) {
		if (1 > $month || 12 < $month) {
			return 0;
		}
		if (!($daysInmonths = self::sGetRealDaysInMonthsOfYear($year))) {
			return 0;
		}
		return $daysInmonths[$month - 1];
	}
	
	/**
	 * 获取该年的天数 
	 * 
	 * @return int 
	 */
	public static function sDaysInYear($year) {
		return self::sIsLeapYear($year) ?  366 : 365;
	}
	
	/**
	 * 取得符合外国人习惯的时间 
	 * @return string
	 */
	public static function getRFCDate() {
		$tz = date('Z');
		$tzs = ($tz < 0) ? '-' : '+';
		$tz = abs($tz);
		$tz = (int) ($tz / 3600) * 100 + ($tz % 3600) / 60;
		return sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);
	}
	/**
	 * 取得符合中国人习惯的日期时间
	 * @return string
	 */
	public static function getChineseDate() {
		list($y, $m, $d, $w, $h, $_h, $i) = explode(' ', date('Y n j w G g i'));
		return sprintf('%s年%s月%s日(%s) %s%s:%s', $y, $m, $d, self::getChineseWeek($w), self::getPeriodOfTime($h), $_h, $i);
	}
	/**
	 * 取得中国的星期
	 * @param int $week 处国人的星期，是一个数值
	 * @return string
	 */
	public static function getChineseWeek($week = null) {
		$week = $week ? $week : (int) date('w', time());
		$weekMap = array("星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
		return $weekMap[$week];
	}
	/**
	 * 取得一天中的时段
	 * @param int $hour 小时
	 * @return string
	 */
	public static function getPeriodOfTime($hour = null) {
		$hour = $hour ? $hour : (int) date('G', time());
		$period = '';
		if (0 <= $hour && 6 > $hour) {
			$period = '凌晨';
		} elseif (6 <= $hour && 8 > $hour) {
			$period = '早上';
		} elseif (8 <= $hour && 11 > $hour) {
			$period = '上午';
		} elseif (11 <= $hour && 13 > $hour) {
			$period = '中午';
		} elseif (13 <= $hour && 15 > $hour) {
			$period = '响午';
		} elseif (15 <= $hour && 18 > $hour) {
			$period = '下午';
		} elseif (18 <= $hour && 20 > $hour) {
			$period = '傍晚';
		} elseif (20 <= $hour && 22 > $hour) {
			$period = '晚上';
		} elseif (22 <= $hour && 23 >= $hour) {
			$period = '深夜';
		}
		return $period;
	}
}
	
	