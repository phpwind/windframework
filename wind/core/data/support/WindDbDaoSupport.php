<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindDbDaoSupport extends WindDaoSupport {
	
	public function init() {}
	public function setTemplate($template) {
		$this->template = $template;
	}
	public function getTemplate() {
		if (null === $this->template) {
			$this->template = new WindDbTemplate();
		}
		return $this->template;
	}
}