<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUrlFilter extends WindFilter {

	/* (non-PHPdoc)
	 * @see WindFilter::preHandle()
	 */
	public function preHandle($request = null, $response = null) {
		$windFactory = $request->getAttribute(WindFrontController::WIND_FACTORY);
		$this->urlHelper = $windFactory->getInstance(COMPONENT_URLHELPER);
		$this->urlHelper->parseUrl();
	}

	/* (non-PHPdoc)
	 * @see WindFilter::postHandle()
	 */
	public function postHandle($request = null, $response = null) {

	}

}

?>