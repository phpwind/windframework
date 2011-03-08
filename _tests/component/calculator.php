<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class Calculator {
	/**
	 * @assert (0, 0) == 0     
	 * @assert (0, 1) == 1    
	 * @assert (1, 0) == 1  
	 * @assert (1, 1) == 2     
	 */
	public function add($a, $b) {
		return $a + $b;
	}
}