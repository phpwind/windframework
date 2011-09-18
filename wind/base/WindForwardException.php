<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindForwardException extends WindException {
	/**
	 * @var WindForward
	 */
	private $forward;

	/**
	 * @param WindForward $forward
	 */
	public function __construct($forward) {
		$this->forward = $forward;
	}

	/**
	 * @return WindForward
	 */
	public function getForward() {
		return $this->forward;
	}

	/**
	 * @param WindForward $forward
	 */
	public function setForward($forward) {
		$this->forward = $forward;
	}

}

?>