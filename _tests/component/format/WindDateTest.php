<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('component/format/WindDate.php');

WindDate::setTimezone('UTC');
class WindDateTest extends BaseTestCase {
	private $monthDays = array('1' => '31', '2' => '28', '3' => '31', '4' => '30', '5' => '31', 
				'6' => '30','7' => '31', '8' => '31', '9' => '30', '10' => '31', '11' => '30', 
				'12' => '31');
    private $t;
	public function setUp() {
		parent::setUp();
		$this->t = new WindDate();
	}
	public function tearDown() {
		parent::tearDown();
	}
	public static function providerDate() {
		return array(
			array('2010', 1, 31, 0, 0, 0),
			array(2011, 1, 1, 12, 10, 1),
			array(1990),
			array(1996, 2, 0, 1, 0, 0),
			array(),
		);
	}
	public static function providerDateOfWeek() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 1),
			array(2011, 1, 1, 12, 10, 1, 7),
			array(2011, 1, 4, 0, 0, 0, 3),
			array(1996, 2, 4, 1, 0, 0, 1),
			array(2007, 1, 31, 1, 0, 0, 4),
		);
	}
	public static function providerWeekOfYear() {
		return array(
			array('2010', 1, 31, 0, 0, 0, date('W', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, date('W', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, date('W', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, date('W', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, date('W', strtotime('2007-1-31'))),
		);
	}
	public static function providerGetYear() {
		return array(
			array('2010', 1, 31, 0, 0, 0, true, date('Y', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, true, date('Y', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, false, date('y', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, true, date('Y', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, false, date('y', strtotime('2007-1-31'))),
		);
	}
	public static function providerGetMonth() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 0, date('m', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, 1, date('n', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, 2, date('M', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, date('M', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, 1, date('n', strtotime('2007-1-31'))),
			array(0, 0, 0, 1, 0, 0, 3, date('m', time())),
		);
	}

	public static function providerGetDay() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 0, date('d', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, 1, date('j', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, 2, date('D', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, date('D', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, 1, date('j', strtotime('2007-1-31'))),
			array(0, 0, 0, 1, 0, 0, 3, date('j', time())),
		);
	}

	public static function providerGetWeek() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 0, date('jS', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, 1, date('w', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, 2, date('S', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, date('S', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, 1, date('w', strtotime('2007-1-31'))),
			array(0, 0, 0, 1, 0, 0, 3, date('N', time())),
		);
	}

	public static function providerGetHours() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 0, true, date('H', strtotime('2010-1-31'))),
			array('2010', 1, 31, 0, 0, 0, 0, false, date('h', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, 1, false, date('g', strtotime('2011-1-1 12:10:1'))),
			array(2007, 1, 31, 1, 0, 0, 1, true, date('G', strtotime('2007-1-31 1:0:0'))),
			array(2011, 1, 4, 0, 0, 0, 2, false, date('H', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, true, date('H', strtotime('1996-2-4 1:0:0'))),
			array(0, 0, 0, 0, 0, 0, 3, false, date('H', time())),
		);
	}
	public static function providerGetMinutes() {
		return array(
			array('2010', 1, 31, 0, 0, 0, date('i', strtotime('2010-1-31 0:0:0'))),
			array(2011, 1, 1, 12, 10, 1, date('i', strtotime('2011-1-1 12:10:1'))),
			array(2007, 1, 31, 1, 0, 0, date('i', strtotime('2007-1-31 1:0:0'))),
			array(2011, 1, 4, 12, 59, 59, date('i', strtotime('2011-1-4 12:59:59'))),
			array(1996, 2, 4, 1, 0, 0, date('i', strtotime('1996-2-4 1:0:0'))),
		);
	}

	public static function providerGetSeconds() {
		return array(
			array('2010', 1, 31, 0, 0, 0, date('s', strtotime('2010-1-31 0:0:0'))),
			array(2011, 1, 1, 12, 10, 1, date('s', strtotime('2011-1-1 12:10:1'))),
			array(2007, 1, 31, 1, 0, 0, date('s', strtotime('2007-1-31 1:0:0'))),
			array(2011, 1, 4, 12, 59, 59, date('s', strtotime('2011-1-4 12:59:59'))),
			array(1996, 2, 4, 1, 0, 0, date('s', strtotime('1996-2-4 1:0:0'))),
			array(0, 0, 0, 0, 0, 6, date('s', strtotime(date('Y-m-d H:i') . ':6'))),
		);
	}

	public static function providergetTimeStamp() {
		return array(
			array(strtotime('2010-1-31 0:0:0')),
			array(strtotime('2011-1-1 12:10:1')),
			array(strtotime('2007-1-31 1:0:0')),
			array(strtotime('2011-1-4 12:59:59')),
			array('1996-2-4 1:0:0', false),
			array('32423'),
		);
	}
	
	public static function providerDateDiff() {
		return array(
			array('y', '2009-1-1', '2010-1-3', 1),
			array('m', '2009-1-1', '2010-1-3', 12),
			array('w', '2009-1-1', '2010-1-1', 52),
			array('d', '2010-1-1', '2010-1-5', 4),
			array('h', '2010-1-1 12:20:0', '2010-1-1 13:40:0', 1),
			array('n', '2010-1-1 12:20:0', '2010-1-1 13:40:0', 80),
			array('s', '2010-1-1 12:20:0', '2010-1-1 13:40:0', 80 * 60),
			array('s', '2010-1-1 12:20:0', '2010-1-1 13:40:0', 80 * 60),
			array('DDD', '2010-1-1 12:20:0', '2010-1-1 13:40:0', 80 * 60),
		);
	}

	public static function providerDateAdd() {
		return array(
			array('y', 1, '2009-1-1', 'Y-m-d', '2010-01-01'),
			array('q', 1, '2009-1-1', 'Y-m-d', '2009-04-01'),
			array('m', 1, '2009-1-1', 'Y-m-d', '2009-02-01'),
			array('w', 1, '2009-1-1', 'Y-m-d', '2009-01-08'),
			array('d', 4, '2009-1-29', 'Y-m-d', '2009-02-02'),
			array('h', 4, '2009-1-29 0:0:0', 'Y-m-d H:i', '2009-01-29 04:00'),
			array('n', 55, '2009-1-29 4:0:0', 'Y-m-d H:i', '2009-01-29 04:55'),
			array('s', 5, '2009-1-29 4:55:55', 'Y-m-d H:i:s', '2009-01-29 04:56:00'),
			array('sdd', 1, '2009-1-29 23:59:59', 'Y-m-d H:i:s', '2009-01-30 00:00:00'),
		);
	}
	
	public static function providerGetDaysInMonthOfYear() {
		return array(
			array('1991'),
			array('1992'),
			array('1993'),
			array('1994'),
			array('1995'),
		);
	}
	
	public static function providerSDaysInMonth() {
		return array(
			array(1, '1991'),
			array(2, '1992'),
			array(2, '1993'),
			array(12, '1994'),
			array(13, '1995'),
		);
	}
	
	private function checkLeap($year) {
		if ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0) {
			return 1;
		}
		return 0;
	}
	private function checkMonthDay ($year, $month) {
		$this->checkLeap($year) && $this->monthDays[2] = 29;
		$r = $this->monthDays[$month];
		$this->monthDays[2] = 28;
	    return $r;
	}
	private function getDaysOfYear($year, $month, $day) {
		$this->checkLeap($year) && $this->monthDays[2] = 29;
		$sum  = 0;
		for ($key = 1; $key < $month; $key ++) {
			$sum += $this->monthDays[$key];
		}
		$sum += $day;
		$this->monthDays[2] = 28;
		return $sum;
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testIsLeapYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		$this->assertTrue($t->isLeapYear() == $this->checkLeap($year));
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testDaysInMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		!$month && ((!$year) ? $month = date('m', time()) : $month = 1);
		$this->assertEquals($this->checkMonthDay($year, $month), $t->daysInMonth());
	}

	/**
	 * @dataProvider providerDate
	 */
	public function testDaysInYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		$days = $this->checkLeap($year) ? 366 : 365;
		$this->assertEquals($days, $t->daysInYear());
	}

	/**
	 * @dataProvider providerDate
	 */
	public function testDayOfYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		!$month && ((!$year) ? $month = date('m', time()) : $month = 1);
		!$day && ((!$year) ? $day = date('d', time()) : $day = 1);
		!$year && $year = date('Y', time());
		$this->assertEquals($this->getDaysOfYear($year, $month, $day), $t->dayOfYear());
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testDayOfMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		!$day && ((!$year) ? $day = date('d', time()) : $day = 1);
		$this->assertEquals($day, $t->dayOfMonth());
	}
	
	/**
	 * @dataProvider providerDateOfWeek
	 */
	public function testDayOfWeek($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $days) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($days, $t->dayOfWeek());
	}
	
	/**
	 * @dataProvider providerWeekOfYear
	 */
	public function testWeekOfYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $days) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($days, $t->weekOfYear());
	}
	
	/**
	 * @dataProvider providerGetYear
	 */
	public function testGetYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $years) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($years, $t->getYear($flag));
	}

	/**
	 * @dataProvider providerGetMonth
	 */
	public function testGetMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getMonth($flag));
	}

	/**
	 * @dataProvider providerGetDay
	 */
	public function testGetDay($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getDay($flag));
	}

	/**
	 * @dataProvider providerGetWeek
	 */
	public function testGetWeek($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getWeek($flag));
	}

	/**
	 * @dataProvider providerGetHours
	 */
	public function testGetHours($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $form, $flag, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getHours($form, $flag));
	}

	/**
	 * @dataProvider providerGetMinutes
	 */
	public function testGetMinutes($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getMinutes());
	}

	/**
	 * @dataProvider providerGetSeconds
	 */
	public function testGetSeconds($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $rt) {
		$t = new WindDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getSeconds());
	}
	
	public function testGetLocalTimeZone() {
		$t = new WindDate();
		$t->getLocalTimeZone();
	}
	
	/**
	 * @dataProvider providergetTimeStamp
	 */
	public function testGetUnixTimeStamp($time, $flag = true) {
		$this->t->setTime($time);
		!$flag && $time = strtotime($time);
		strtotime($time) && $this->assertEquals($time, $this->t->getUnixTimeStamp());
	}
	
	public function testGetNow() {
		$t2 = $this->t->getNow();
		$this->assertTrue($t2 instanceof WindDate);
	}
	
	public function testToString() {
		$t = new WindDate();
		$this->assertEquals(date('Y-m-d H:i'), $t->toString('Y-m-d H:i'));
		$this->assertEquals(date('Y-m-d H:i'), date('Y-m-d H:i', strtotime($t)));
	}
	
	public function testToUTCString() {
		$t = time();
		$this->t->setTime($t);
		$this->assertEquals(date('D, d M y H:i:s e', $t), $this->t->toUTCString());
		WindDate::setTimeZone('Asia/Shanghai');
		$this->assertTrue(strrpos($this->t->toUTCString(), 'UTC') != false);
	}
	
	public function testTimeZone() {
		$timeZone = WindDate::getTimeZone();
		WindDate::setTimezone('Asia/Shanghai');
		$this->assertEquals('Asia/Shanghai', WindDate::getTimeZone());
	}
	
	public function testFormat() {
		$this->assertEquals(date('Y-m-d'), WindDate::format('Y-m-d'));
		$this->assertEquals('2005-05-12', WindDate::format('Y-m-d', strtotime('2005-5-12')));
	}
	
	public function testDatePart() {
		$this->assertEquals(date('y/m/d H', strtotime('2005')), WindDate::datePart('y/m/d H', '2005'));
	}
	
	/**
	 * @dataProvider providerDateDiff
	 */
	public function testDateDiff($style, $start, $end, $rt) {
		$this->assertEquals($rt, WindDate::dateDiff($style, $start, $end));
	}
	
	/**
	 * @dataProvider providerDateAdd
	 */
	public function testDateAdd($style, $value, $date, $format, $rt) {
		$this->assertEquals($rt, WindDate::dateAdd($style, $value, $date, $format));
	}
	
	private function checkArray($rightArr, $waitForCheckArr) {
		$this->assertEquals(count($rightArr), count($waitForCheckArr));
		
	}
	
	/**
	 * @dataProvider providerGetDaysInMonthOfYear
	 */
	public function testSGetRealDaysInMonthsOfYear($year) {
		if ($this->checkLeap($year)) {
			$this->monthDays[2] = 29;
		} else {
			$this->monthDays[2] = 28;
		}
		$array = WindDate::sGetRealDaysInMonthsOfYear($year);
		$this->assertEquals(count($this->monthDays), count($array));
		$this->assertEquals($this->monthDays[2], $array[1]);
	}
	
	/**
	 * @dataProvider providerSDaysInMonth
	 */
	public function testSDaysInMonth($month, $year) {
		if ($this->checkLeap($year)) {
			$this->monthDays[2] = 29;
		} else {
			$this->monthDays[2] = 28;
		}
		$day = (1 > $month || 12 < $month) ?  0 : $this->monthDays[$month];
		$this->assertEquals($day, WindDate::sDaysInMonth($month, $year));
	}

	/**
	 * @dataProvider providerGetDaysInMonthOfYear
	 */
	public function testSDaysInYear($year) {
		$days = $this->checkLeap($year) ? 366 : 365;
		$this->assertEquals($days, WindDate::sDaysInYear($year));
	}
}