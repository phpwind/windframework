<?php

L::import('WIND:core.filter.WindFilter');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindLoggerFilter extends WindFilter {

	const WIND_LOGGER = 'windLogger';

	/* (non-PHPdoc)
	 * @see WindFilter::preHandle()
	 */
	public function preHandle($request = null, $response = null) {
		$this->initWindLogger($request);
	}

	/* (non-PHPdoc)
	 * @see WindFilter::postHandle()
	 */
	public function postHandle($request = null, $response = null) {
		if ($this->logger instanceof WindLogger) $this->logger->flush();
	}

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 */
	private function initWindLogger($request) {
		$this->logger = $request->getAttribute(WindFrontController::WIND_FACTORY)->getInstance(self::WIND_LOGGER);
	}

}

?>