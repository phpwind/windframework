<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 异常处理机制
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WException extends exception {
	private $innerException = null;
	/**
	 * 异常构造函数
	 * @param $message		     异常信息
	 * @param $code			     异常代号
	 * @param $innerException 内部异常
	 */
	public function __construct($message = '',$code=0,exception $innerException = null) {
        parent::__construct($message,$code);
        $this->innerException = $innerException;
    }
    
    /**
     * 取得内部异常
     */
    public function getInnerException(){
    	return $this->innerException;
    }
    
    /**
     * 取得异常堆栈信息
     */
    public function getStackTrace(){
    	if($this->innerException){
	    	$thisTrace = $this->getTrace();
	    	$innerTrace = get_class($this->innerException) == __CLASS__ ? $this->innerException->getStackTrace() :$this->innerException->getTrace();
	    	foreach($innerTrace as $trace) $thisTrace[] = $trace;
	    	return $thisTrace;
    	}else{
    		return $this->getTrace();
    	}
    	return array();
    }
}