<?php
/**
*@author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-7
*@link http://www.phpwind.com
*@copyright Copyright &copy; 2003-2110 phpwind.com
*@license 
*/


require_once('component/utility/date/WindGeneralDate.php');
require_once('component/utility/date/WindDate.php');

WindDate::setTimezone('UTC');
class WindGeneralDateTest extends BaseTestCase {
	private $monthDays = array('1' => '31', '2' => '28', '3' => '31', '4' => '30', '5' => '31', 
				'6' => '30','7' => '31', '8' => '31', '9' => '30', '10' => '31', '11' => '30', 
				'12' => '31');
    private $t;
	public function setUp() {
		parent::setUp();
		$this->t = new WindGeneralDate();
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
			array(2011, 1, 4, 0, 0, 0, 2, date('jS', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, date('jS', strtotime('1996-2-4'))),
			array(2007, 1, 31, 1, 0, 0, 1, date('j', strtotime('2007-1-31'))),
			array(0, 0, 0, 1, 0, 0, 3, date('j', time())),
		);
	}

	public static function providerGetWeek() {
		return array(
			array('2010', 1, 31, 0, 0, 0, 0, date('w', strtotime('2010-1-31'))),
			array(2011, 1, 1, 12, 10, 1, 1, date('w', strtotime('2011-1-1'))),
			array(2011, 1, 4, 0, 0, 0, 2, date('D', strtotime('2011-1-4'))),
			array(1996, 2, 4, 1, 0, 0, 2, date('D', strtotime('1996-2-4'))),
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
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		$this->assertTrue($t->isLeapYear() == $this->checkLeap($year));
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testGetDaysInMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		!$month && ((!$year) ? $month = date('m', time()) : $month = 1);
		$this->assertEquals($this->checkMonthDay($year, $month), $t->getDaysInMonth());
	}

	/**
	 * @dataProvider providerDate
	 */
	public function testGetDaysInYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		if ($year == null) $year = date('Y');
		$days = $this->checkLeap($year) ? 366 : 365;
		$this->assertEquals($days, $t->getDaysInYear());
	}

	/**
	 * @dataProvider providerDate
	 */
	public function testGetDayOfYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		!$month && ((!$year) ? $month = date('m', time()) : $month = 1);
		!$day && ((!$year) ? $day = date('d', time()) : $day = 1);
		!$year && $year = date('Y', time());
		$this->assertEquals($this->getDaysOfYear($year, $month, $day), $t->getDayOfYear());
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testGetDayOfMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		!$day && ((!$year) ? $day = date('d', time()) : $day = 1);
		$this->assertEquals($day, $t->GetDayOfMonth());
	}
	
	/**
	 * @dataProvider providerDateOfWeek
	 */
	public function testGetDayOfWeek($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $days) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($days, $t->getDayOfWeek());
	}
	
	/**
	 * @dataProvider providerWeekOfYear
	 */
	public function testGetWeekOfYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $days) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($days, $t->getWeekOfYear());
	}
	
	/**
	 * @dataProvider providerGetYear
	 */
	public function testGetYear($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $years) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($years, $t->getYear($flag));
	}

	/**
	 * @dataProvider providerGetMonth
	 */
	public function testGetMonth($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getMonth($flag));
	}

	/**
	 * @dataProvider providerGetDay
	 */
	public function testGetDay($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getDay($flag));
	}

	/**
	 * @dataProvider providerGetWeek
	 */
	public function testGetWeek($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $flag, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getWeek($flag));
	}

	/**
	 * @dataProvider providerGetHours
	 */
	public function testGetHours($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $form, $flag, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		if($flag){
			$this->assertEquals($rt, $t->get24Hours($form));
		}else{
			$this->assertEquals($rt, $t->get12Hours($form));
		}
	}

	/**
	 * @dataProvider providerGetMinutes
	 */
	public function testGetMinutes($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getMinutes());
	}

	/**
	 * @dataProvider providerGetSeconds
	 */
	public function testGetSeconds($year = null, $month = null, $day = null, $hours = null, $minutes = null, $second = null, $rt) {
		$t = new WindGeneralDate($year, $month, $day, $hours, $minutes, $second);
		$this->assertEquals($rt, $t->getSeconds());
	}
	
	public function testGetLocalTimeZone() {
		$t = new WindGeneralDate();
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
		$this->assertTrue($t2 instanceof WindGeneralDate);
	}
	
	public function testToString() {
		$t = new WindGeneralDate();
		$this->assertEquals(date('Y-m-d H:i'), $t->toString('Y-m-d H:i'));
		$this->assertEquals(date('Y-m-d H:i'), date('Y-m-d H:i', strtotime($t)));
	}
	
	
}