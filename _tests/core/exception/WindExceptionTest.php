<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('core/exception/WindException.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindExceptionTest extends PHPUnit_Framework_TestCase {
	protected $message = 'test WindException';
	
	public function testWindException(){
		try{
	    	throw new WindException($this->message);
	    } catch (WindException $exception) {
	    	return true;
	    }
	    $this->fail($this->message);
	}
	
	public function testGetInnerException(){
		
		try{
	    	throw new WindException($this->message,3,new Exception($this->message));
	    } catch (WindException $exception) {
	    	$innerException = $exception->getInnerException();
	    	if($innerException instanceof Exception){
	    		return true;
	    	}
	    }
	    $this->fail($this->message);
	}
	
	public function testGetStackTrace(){
		try{
	    	throw new WindException($this->message,3);
	    } catch (WindException $exception) {
	    	$trace = $exception->getStackTrace();
	    	if($trace && is_array($trace)){
	    		return true;
	    	}
	    	
	    }
	    $this->fail($this->message);
	}
	public function testGetStackTraceWithInner(){
		try{
	    	throw new WindException($this->message,3,new Exception($this->message));
	    } catch (WindException $exception) {
	    	$trace = $exception->getStackTrace();
	    	if($trace && is_array($trace)){
	    		return true;
	    	}
	    	
	    }
	    $this->fail($this->message);
	}
		
}