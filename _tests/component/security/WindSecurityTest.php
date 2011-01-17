<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/security/WindSecurity.php');

class WindSecurityTest extends BaseTestCase {
	public function setUp() {
		parent::setUp();
	}
	public function tearDown() {
		parent::tearDown();
	}
	public static function htmlData() {
		return array(
			array('5>4', '5&gt;4'),
			array('3<13', '3&lt;13'),
			array('D&G', 'D&amp;G'),
			array('i say:"hello!"', 'i say:&quot;hello!&quot;'),
			array("my name is A'B", 'my name is A&#039;B'),
			array('', ''),
			array("<a href='test'>Test</a>", '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'),
		//	array(array('cin>>a'), array('cin&gt;&gt;a')),
			array(111, 111),
		);
	}
	
	public static function tagsData() {
		return array(
			array('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>', '', 'Test paragraph. Other text'),
			array('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>', '<p><a>', '<p>Test paragraph.</p> <a href="#fragment">Other text</a>'),
			array('<p>Test paragraph.</p><?php echo "fs"; ?><br/>', '', 'Test paragraph.'),
			array('<p>Test paragraph.</p><?php echo "fs"; ?><br/>', '<br/>', 'Test paragraph.<br/>'),
		//	array(array('<html><head><title>Me</title></head></html>'), '', array('Me')),
			array(121334, '', 121334),
		);
	}
	
	public static function addSlashesData() {
		return array(
			array('Is your name O\'reilly?', 'Is your name O\\\'reilly?'),
			array('Is your name \\ssss', 'Is your name \\\\ssss'),
			array(11111, 11111),
			array('', ''),
		);
	}
	
	public static function addSlashesDataArray() {
		return array(
			array(array(), array()),
			array(array("Is your name \\ssss"), array("Is your name \\\\ssss")),
			array(array(array('Is your name O\'reilly?')), array(array('Is your name O\\\'reilly?'))),
			array(array(11111), array(11111)),
			array(array(''), array('')),
		);
	}
	
	/**
	 * @dataProvider htmlData
	 */
	public function testEscapeHTML($source, $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::escapeHTML($source));
		} else {
			$this->assertEquals($result, WindSecurity::escapeHTML($source));
		}
	}
	
	/**
	 * @dataProvider tagsData
	 */
	public function testStripTags($source, $option = '', $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::stripTags($source, $option));
		} else {
			$this->assertEquals($result, WindSecurity::stripTags($source, $option));
		}
	}
	
	public function testAddSlashesFromGPC() {
		$_GET['t1'] = 'Is your name O"reilly?';
		$r1 = "Is your name O\"reilly?";
		$_GET['t2'] = 'Is your name \ssss';
		$r2 = 'Is your name \\ssss';
		$this->assertEquals($r1, WindSecurity::addSlashesFromGPC($_GET['t1']));
		$this->assertEquals($r2, WindSecurity::addSlashesFromGPC($_GET['t2']));
	}
	
	/**
	 * @dataProvider addSlashesData
	 */
	public function testAddSlashesFromDF($source, $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::addSlashesFromDF($source));
		} else {
			$this->assertEquals($result, WindSecurity::addSlashesFromDF($source));
		}
	}

	/**
	 * @dataProvider addSlashesData
	 */
	public function testAddSlashesFromString($source, $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::addSlashesFromString($source));
		} else {
			$this->assertEquals($result, WindSecurity::addSlashesFromString($source));
		}
	}
	
	/**
	 * @dataProvider addSlashesDataArray
	 */
	public function testAddSlashesFromArray($source, $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::addSlashesFromArray($source));
		} else {
			$this->assertEquals($result, WindSecurity::addSlashesFromArray($source));
		}
	}
	
	/**
	 * @dataProvider addSlashesData
	 */
	public function testStripSlashesWithString($result, $source) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::stripSlashes($source));
		} else {
			$this->assertEquals($result, WindSecurity::stripSlashes($source));
		}
	}

	/**
	 * @dataProvider addSlashesDataArray
	 */
	public function testStripSlashesWithArray($result, $source) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::stripSlashes($source));
		} else {
			$this->assertEquals($result, WindSecurity::stripSlashes($source));
		}
	}
	
	public function testEscapePath() {
		$path ='D:/path/php/test.php';
		$this->assertEquals($path, WindSecurity::escapePath($path, true));
		$path = '../path/test.php';
		$this->assertEquals($path, WindSecurity::escapePath($path, false));
		$path = '..://\0/path/test.php';
	}
	
	public static function dirData() {
		return array(
			array('D://ppp//test/', 'D:/ppp/test'),
			array('/var/p/test/*=$p', '/var/p/test/*p'),
			array('/\'%/var&/p/test`/;\'', '/var/p/test'),
			array('', ''),
		);
	}
	/**
	 * @dataProvider dirData
	 */
	public function testEscapeDir($resource, $result) {
		$this->assertEquals($result, WindSecurity::escapeDir($resource));
	}
	
	public static function stringData() {
		return array(
			array('abcdefgh', 'abcdefgh'),
			array("\0abdc%00efgh\r  %3C&#48z;\t", "abdcefgh&nbsp;&nbsp;&lt;&amp;#48z;    "),
			array("$#666efg;&#pp;", "$#666efg;&amp;#pp;"),
		);
	}
	
	/**
	 * @dataProvider stringData
	 */
	public function testEscapeString($source, $result) {
		$this->assertEquals($result, WindSecurity::escapeString($source));
	}
	
	public static function escapeData() {
		return array(
			array('111', '111'),
			array(111, 111),
			array("\0abdc%00efgh\r  %3C&#48z;\t", "abdcefgh&nbsp;&nbsp;&lt;&amp;#48z;    "),
			array(array('abcdefgh', "\0abdc%00efgh\r  %3C&#48z;\t", "$#666efg;&#pp;"),
				  array('abcdefgh', "abdcefgh&nbsp;&nbsp;&lt;&amp;#48z;    ", "$#666efg;&amp;#pp;")),
		);
	}
	/**
	 * @dataProvider escapeData
	 */
	public function testEscapeChar($source, $result) {
		if (is_array($source)) {
			$this->assertArrayEquals($result, WindSecurity::escapeChar($source));
		} else {
			$this->assertEquals($result, WindSecurity::escapeChar($source));
		}
	}
	
	public static function quoteData() {
		return array(
			array("Hello world. (can you hear me?)", 'Hello world\. \(can you hear me\?\)'),
			array("Hello world+ [can you hear me?]", 'Hello world\+ \[can you hear me\?\]'),
			array("Yes. $ I can hear you!", 'Yes\. \$ I can hear you!'),
			array("WelCome!^", 'WelCome!\^'),
		);
	}
	/**
	 * . \ + * ? [ ^ ] ( $ )
	 * @dataProvider quoteData
	 */
	public function testQuotemeta($source, $value) {
		$this->assertEquals($value, WindSecurity::quotemeta($source));
	}
}