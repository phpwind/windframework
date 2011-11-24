<?php
/**
 * 日期时间和数字格式转换
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package i18n
 */
class WindLocale {
	
	private $_cachePrefix = 'Wind.i18n.WindLocale';
	private $dateFormatType = 'medium';
	private $timeFormatType = 'medium';
	private $language;
	/**
	 * 格式存储池
	 *
	 * @var array
	 */
	protected $data = array();
	/**
	 * 数据本地化格式data路径
	 *
	 * @var string
	 */
	protected $dataPath = 'WIND:i18n.data';
	
	private static $methods = array(
		'G' => 'formatEra', 
		'y' => 'formatYear', 
		'M' => 'formatMonth', 
		'L' => 'formatMonth', 
		'd' => 'formatDay', 
		'h' => 'formatHour12', 
		'H' => 'formatHour24', 
		'm' => 'formatMinutes', 
		's' => 'formatSeconds', 
		'E' => 'formatDayInWeek', 
		'c' => 'formatDayInWeek', 
		'e' => 'formatDayInWeek', 
		'D' => 'formatDayInYear', 
		'F' => 'formatDayInMonth', 
		'w' => 'formatWeekInYear', 
		'W' => 'formatWeekInMonth', 
		'a' => 'formatPeriod', 
		'k' => 'formatHourInDay', 
		'K' => 'formatHourInPeriod', 
		'z' => 'formatTimeZone', 
		'Z' => 'formatTimeZone', 
		'v' => 'formatTimeZone');

	/**
	 * 构造方法
	 *
	 * @param string $language
	 */
	public function __construct($language) {
		$this->language = $language;
		$this->initFormatData();
	}

	/**
	 * 转换时间格式
	 *
	 * @param int $timestamp
	 * @return string
	 */
	public function formatDate($timestamp) {
		$dateArray = getdate($timestamp);
		if (empty($this->data)) return $timestamp;
		list($dateFormat, $timeFormat) = array(
			$this->getDataAttribute('dateFormats', $this->dateFormatType), 
			$this->getDataAttribute('timeFormats', $this->timeFormatType));
		$result = '';
		!empty($dateFormat) && $result .= $this->doFormat($dateFormat, $dateArray) . ' ';
		!empty($timeFormat) && $result .= $this->doFormat($timeFormat, $dateArray) . ' ';
		return $result ? $result : $timestamp;
	}

	/**
	 * 进行格式转换
	 *
	 * @param string $format
	 * @param array $dateArray
	 * @return string
	 */
	private function doFormat($format, $dateArray) {
		$results = $this->parseDateFormat($format);
		foreach ($results as &$v) {
			is_array($v) && $v = call_user_func_array(array($this, $v[0]), array($v[1], $dateArray));
		}
		return implode(' ', $results);
	}

	/**
	 * 格式化数值
	 *
	 * @param int $number
	 */
	public function formatNumber($number) {
		$format = $this->getDataAttribute('decimalFormat');
		$format = $this->parseNumberFormat($format);
		return $this->doNumberformat($format, $number);
	}

	/**
	 * 格式化金额
	 *
	 * @param int $number
	 * @param string $currency
	 * @return string
	 */
	public function formatCurrency($number, $currency) {
		$format = $this->getDataAttribute('currencyFormat');
		$format = $this->parseNumberFormat($format);
		$number = $this->doNumberformat($format, $number);
		($symbol = $this->getDataAttribute('currencySymbols', $currency)) && $currency = $symbol;
		return str_replace('¤', $currency, $number);
	}

	/**
	 * 格式化百分值
	 *
	 * @param int $number
	 * @return string
	 */
	public function formatPercentage($number) {
		$format = $this->getDataAttribute('percentFormat');
		$format = $this->parseNumberFormat($format);
		return $this->doNumberformat($format, $number);
	}

	/**
	 * 初始化本地化格式数据
	 *
	 * @return boolean
	 */
	private function initFormatData() {
		if (empty($this->data)) {
			/* @var $parser WindConfigParser */
			$parser = Wind::getApp()->getComponent('configParser');
			$dataFile = Wind::getRealPath($this->dataPath . '.' . $this->language);
			if (!is_file($dataFile)) return false;
			$cacheKey = $this->_cachePrefix . $dataFile . filemtime($dataFile);
			$cache = Wind::getApp()->getComponent('windCache');
			$this->data = $parser->parse($dataFile, $cacheKey, '', $cache);
		}
	}

	/**
	 * 获取本地化数据
	 *
	 * @return mixed
	 */
	private function getDataAttribute() {
		$temp = $this->data;
		foreach (func_get_args() as $arg) {
			if (is_array($temp) && isset($temp[$arg]))
				$temp = $temp[$arg];
			else
				return '';
		}
		return $temp;
	}

	private function parseDateFormat($pattern) {
		$results = array();
		$n = strlen($pattern);
		$isLiteral = false;
		$literal = '';
		for ($i = 0; $i < $n; ++$i) {
			$c = $pattern[$i];
			if ($c === "'") {
				if ($i < $n - 1 && $pattern[$i + 1] === "'") {
					$results[] = "'";
					$i++;
				} else if ($isLiteral) {
					$results[] = $literal;
					$literal = '';
					$isLiteral = false;
				} else {
					$isLiteral = true;
					$literal = '';
				}
			} else if ($isLiteral)
				$literal .= $c;
			else {
				for ($j = $i + 1; $j < $n; ++$j) {
					if ($pattern[$j] !== $c) break;
				}
				$p = str_repeat($c, $j - $i);
				if (isset(self::$methods[$c]))
					$results[] = array(self::$methods[$c], $p);
				else
					$results[] = $p;
				$i = $j - 1;
			}
		}
		if ($literal !== '') $results[] = $literal;
		
		return $results;
	}

	private function parseNumberFormat($pattern) {
		$format = array();
		
		$patterns = explode(';', $pattern);
		$format['positivePrefix'] = $format['positiveSuffix'] = $format['negativePrefix'] = $format['negativeSuffix'] = '';
		if (preg_match('/^(.*?)[#,\.0]+(.*?)$/', $patterns[0], $matches)) {
			$format['positivePrefix'] = $matches[1];
			$format['positiveSuffix'] = $matches[2];
		}
		
		if (isset($patterns[1]) && preg_match('/^(.*?)[#,\.0]+(.*?)$/', $patterns[1], $matches)) {
			$format['negativePrefix'] = $matches[1];
			$format['negativeSuffix'] = $matches[2];
		} else {
			$format['negativePrefix'] = $this->getDataAttribute('numberSymbols', 'minusSign') . $format['positivePrefix'];
			$format['negativeSuffix'] = $format['positiveSuffix'];
		}
		$pat = $patterns[0];
		
		if (strpos($pat, '%') !== false)
			$format['multiplier'] = 100;
		else if (strpos($pat, '‰') !== false)
			$format['multiplier'] = 1000;
		else
			$format['multiplier'] = 1;
		
		if (($pos = strpos($pat, '.')) !== false) {
			if (($pos2 = strrpos($pat, '0')) > $pos)
				$format['decimalDigits'] = $pos2 - $pos;
			else
				$format['decimalDigits'] = 0;
			if (($pos3 = strrpos($pat, '#')) >= $pos2)
				$format['maxDecimalDigits'] = $pos3 - $pos;
			else
				$format['maxDecimalDigits'] = $format['decimalDigits'];
			$pat = substr($pat, 0, $pos);
		} else {
			$format['decimalDigits'] = 0;
			$format['maxDecimalDigits'] = 0;
		}
		
		$p = str_replace(',', '', $pat);
		if (($pos = strpos($p, '0')) !== false)
			$format['integerDigits'] = strrpos($p, '0') - $pos + 1;
		else
			$format['integerDigits'] = 0;
		
		$p = str_replace('#', '0', $pat);
		if (($pos = strrpos($pat, ',')) !== false) {
			$format['groupSize1'] = strrpos($p, '0') - $pos;
			if (($pos2 = strrpos(substr($p, 0, $pos), ',')) !== false)
				$format['groupSize2'] = $pos - $pos2 - 1;
			else
				$format['groupSize2'] = 0;
		} else
			$format['groupSize1'] = $format['groupSize2'] = 0;
		
		return $format;
	}

	protected function doNumberformat($format, $value) {
		$negative = $value < 0;
		$value = abs($value * $format['multiplier']);
		if ($format['maxDecimalDigits'] >= 0) $value = round($value, $format['maxDecimalDigits']);
		$value = "$value";
		if (($pos = strpos($value, '.')) !== false) {
			$integer = substr($value, 0, $pos);
			$decimal = substr($value, $pos + 1);
		} else {
			$integer = $value;
			$decimal = '';
		}
		
		if ($format['decimalDigits'] > strlen($decimal)) $decimal = str_pad($decimal, 
			$format['decimalDigits'], '0');
		if (strlen($decimal) > 0) $decimal = $this->getDataAttribute('numberSymbols', 'decimal') . $decimal;
		
		$integer = str_pad($integer, $format['integerDigits'], '0', STR_PAD_LEFT);
		if ($format['groupSize1'] > 0 && strlen($integer) > $format['groupSize1']) {
			$str1 = substr($integer, 0, -$format['groupSize1']);
			$str2 = substr($integer, -$format['groupSize1']);
			$size = $format['groupSize2'] > 0 ? $format['groupSize2'] : $format['groupSize1'];
			$str1 = str_pad($str1, (int) ((strlen($str1) + $size - 1) / $size) * $size, ' ', 
				STR_PAD_LEFT);
			$integer = ltrim(
				implode($this->getDataAttribute('numberSymbols', 'group'), str_split($str1, $size))) . $this->getDataAttribute(
				'numberSymbols', 'group') . $str2;
		}
		
		if ($negative)
			$number = $format['negativePrefix'] . $integer . $decimal . $format['negativeSuffix'];
		else
			$number = $format['positivePrefix'] . $integer . $decimal . $format['positiveSuffix'];
		
		return strtr($number, 
			array(
				'%' => $this->getDataAttribute('numberSymbols', 'percentSign'), 
				'‰' => $this->getDataAttribute('numberSymbols', 'perMille')));
	}

	private function formatYear($pattern, $date) {
		$year = $date['year'];
		if ($pattern === 'yy')
			return str_pad($year % 100, 2, '0', STR_PAD_LEFT);
		else
			return str_pad($year, strlen($pattern), '0', STR_PAD_LEFT);
	}

	private function formatMonth($pattern, $date) {
		$month = $date['mon'];
		switch ($pattern) {
			case 'M':
				return $month;
			case 'MM':
				return str_pad($month, 2, '0', STR_PAD_LEFT);
			case 'MMM':
				return $this->getDataAttribute('monthNamesSA', 'abbreviated', $month);
			case 'MMMM':
				return $this->getDataAttribute('monthNamesSA', 'wide', $month);
			case 'MMMMM':
				return $this->getDataAttribute('monthNamesSA', 'narrow', $month);
			case 'L':
				return $month;
			case 'LL':
				return str_pad($month, 2, '0', STR_PAD_LEFT);
			case 'LLL':
				return $this->getDataAttribute('monthNames', 'abbreviated', $month);
			case 'LLLL':
				return $this->getDataAttribute('monthNames', 'wide', $month);
			case 'LLLLL':
				return $this->getDataAttribute('monthNames', 'narrow', $month);
			default:
				return '';
		}
	}

	private function formatDay($pattern, $date) {
		$day = $date['mday'];
		if ($pattern === 'd')
			return $day;
		else if ($pattern === 'dd')
			return str_pad($day, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatDayInYear($pattern, $date) {
		$day = $date['yday'];
		if (($n = strlen($pattern)) <= 3)
			return str_pad($day, $n, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatDayInMonth($pattern, $date) {
		if ($pattern === 'F')
			return (int) (($date['mday'] + 6) / 7);
		else
			return '';
	}

	private function formatDayInWeek($pattern, $date) {
		$day = $date['wday'];
		switch ($pattern) {
			case 'E':
			case 'EE':
			case 'EEE':
			case 'eee':
				return $this->getDataAttribute('weekDayNamesSA', 'abbreviated', $day);
			case 'EEEE':
			case 'eeee':
				return $this->getDataAttribute('weekDayNamesSA', 'wide', $day);
			case 'EEEEE':
			case 'eeeee':
				return $this->getDataAttribute('weekDayNamesSA', 'narrow', $day);
			case 'e':
			case 'ee':
			case 'c':
				return $day ? $day : 7;
			case 'ccc':
				return $this->getDataAttribute('weekDayNames', 'abbreviated', $day);
			case 'cccc':
				return $this->getDataAttribute('weekDayNames', 'wide', $day);
			case 'ccccc':
				return $this->getDataAttribute('weekDayNames', 'narrow', $day);
			default:
				return '';
		}
	}

	private function formatPeriod($pattern, $date) {
		if ($pattern === 'a') {
			if (intval($date['hours'] / 12))
				return $this->getDataAttribute('pmName');
			else
				return $this->getDataAttribute('amName');
		} else
			return '';
	}

	private function formatHour24($pattern, $date) {
		$hour = $date['hours'];
		if ($pattern === 'H')
			return $hour;
		else if ($pattern === 'HH')
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatHour12($pattern, $date) {
		$hour = $date['hours'];
		$hour = ($hour == 12 | $hour == 0) ? 12 : ($hour) % 12;
		if ($pattern === 'h')
			return $hour;
		else if ($pattern === 'hh')
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatHourInDay($pattern, $date) {
		$hour = $date['hours'] == 0 ? 24 : $date['hours'];
		if ($pattern === 'k')
			return $hour;
		else if ($pattern === 'kk')
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatHourInPeriod($pattern, $date) {
		$hour = $date['hours'] % 12;
		if ($pattern === 'K')
			return $hour;
		else if ($pattern === 'KK')
			return str_pad($hour, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatMinutes($pattern, $date) {
		$minutes = $date['minutes'];
		if ($pattern === 'm')
			return $minutes;
		else if ($pattern === 'mm')
			return str_pad($minutes, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatSeconds($pattern, $date) {
		$seconds = $date['seconds'];
		if ($pattern === 's')
			return $seconds;
		else if ($pattern === 'ss')
			return str_pad($seconds, 2, '0', STR_PAD_LEFT);
		else
			return '';
	}

	private function formatWeekInYear($pattern, $date) {
		if ($pattern === 'w')
			return @date('W', @mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']));
		else
			return '';
	}

	private function formatWeekInMonth($pattern, $date) {
		if ($pattern === 'W')
			return @date('W', @mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year'])) - date(
				'W', mktime(0, 0, 0, $date['mon'], 1, $date['year'])) + 1;
		else
			return '';
	}

	private function formatTimeZone($pattern, $date) {
		if ($pattern[0] === 'z' || $pattern[0] === 'v')
			return @date('T', 
				@mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], 
					$date['mday'], $date['year']));
		elseif ($pattern[0] === 'Z')
			return @date('O', 
				@mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], 
					$date['mday'], $date['year']));
		else
			return '';
	}

	private function formatEra($pattern, $date) {
		$era = $date['year'] > 0 ? 1 : 0;
		switch ($pattern) {
			case 'G':
			case 'GG':
			case 'GGG':
				return $this->getDataAttribute('eraNames', 'abbreviated', $era);
			case 'GGGG':
				return $this->getDataAttribute('eraNames', 'wide', $era);
			case 'GGGGG':
				return $this->getDataAttribute('eraNames', 'narrow', $era);
			default:
				return '';
		}
	}
}
