<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class ForWindClassDefinition {
	private $message = '';
	
	public function __construct() {
		$this->message = 'test';
	}
	
	public static function factory() {
		return new self();
	}
	
	public function init() {
		$this->message = 'new';
	}
		
	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param field_type $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}
	
}