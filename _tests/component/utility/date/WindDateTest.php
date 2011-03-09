<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('component/utility/date/WindDate.php');

WindDate::setTimezone('UTC');
class WindDateTest extends BaseTestCase {
	private $monthDays = array('1' => '31', '2' => '28', '3' => '31', '4' => '30', '5' => '31', 
				'6' => '30','7' => '31', '8' => '31', '9' => '30', '10' => '31', '11' => '30', 
				'12' => '31');
	public function setUp() {
		parent::setUp();
	}
	public function tearDown() {
		parent::tearDown();
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
	public function testGetRealDaysInMonthsOfYear($year) {
		if ($this->checkLeap($year)) {
			$this->monthDays[2] = 29;
		} else {
			$this->monthDays[2] = 28;
		}
		$array = WindDate::getRealDaysInMonthsOfYear($year);
		$this->assertEquals(count($this->monthDays), count($array));
		$this->assertEquals($this->monthDays[2], $array[1]);
	}
	
	/**
	 * @dataProvider providerSDaysInMonth
	 */
	public function testGetDaysInMonth($month, $year) {
		if ($this->checkLeap($year)) {
			$this->monthDays[2] = 29;
		} else {
			$this->monthDays[2] = 28;
		}
		$day = (1 > $month || 12 < $month) ?  0 : $this->monthDays[$month];
		$this->assertEquals($day, WindDate::getDaysInMonth($month, $year));
	}

	/**
	 * @dataProvider providerGetDaysInMonthOfYear
	 */
	public function testGetDaysInYear($year) {
		$days = $this->checkLeap($year) ? 366 : 365;
		$this->assertEquals($days, WindDate::getDaysInYear($year));
	}
	
	public function testGetUTCDate() {
		$t = time();
		WindDate::setTimeZone('UTC');
		$this->assertEquals(date('D, d M y H:i:s e', $t), WindDate::getUTCDate($t));
		$this->assertTrue(strrpos(WindDate::getUTCDate($t), 'UTC') != false);
	}
	
	public function testGetLastDate(){
		$time = time();
		$this->assertEquals('10秒前',array_shift(WindDate::getLastDate($time-10)));
		$this->assertEquals('2分钟前',array_shift(WindDate::getLastDate($time-61)));
		$this->assertEquals('2小时前',array_shift(WindDate::getLastDate($time-3601)));
		$this->assertEquals('昨天 '.WindDate::format('H:i',$time),array_shift(WindDate::getLastDate($time-86401)));
		$this->assertEquals('前天 '.WindDate::format('H:i',$time),array_shift(WindDate::getLastDate($time-172801)));
		$this->assertEquals(WindDate::format('m-d',$time-350000),array_shift(WindDate::getLastDate($time-350000)));
		$this->assertEquals(WindDate::format('Y-m-d',$time-11350000),array_shift(WindDate::getLastDate($time-11350000)));
		$this->assertEquals(WindDate::format('Y-m-d H:i',$time),array_pop(WindDate::getLastDate($time-10,null,'Y-m-d H:i')));
	}
	
	public function testGetMicroTime(){
		$microtTime = microtime();
		$this->assertEquals(array_sum(explode(' ', $microtTime)),WindDate::getMicroTime(null,$microtTime));
	}
	
	public function testGetChinaDate(){
		$this->assertEquals('2011年3月9日(星期三) 中午12:07',WindDate::getChinaDate('1299672440'));
	}
}