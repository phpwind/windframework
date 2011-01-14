<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindClassDefinitionTest extends BaseTestCase {
	public function setUp() {
		parent::setUp();
		require_once ('core/factory/WindClassDefinition.php');
	}
	public function tearDown() {
		parent::tearDown();
	}
	private function getTestData() {
		return array(
            'path' => '',
            'factory-method' => 'factory',
            'init-method' => 'new',
            'scope' => 'singleton',
            'properties' => array(
                'name' => array(
                      'value' => 'xxx'
                 ),
                 'key' => array(
                      'value' => 'key'
                 ),
            ),
            'constructor-arg' => array(
                'ref' => 'haha'
            ),
			'import' => array(
                'resource' => 'WIND:core.WindView'
            ),
        );
	}
}