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
class WindScopeFilter extends WindFilter {

	/* (non-PHPdoc)
	 * @see WindFilter::preHandle()
	 */
	public function preHandle(WindHttpRequest $request = null, WindHttpResponse $response = null) {
		$this->windFactory = $request->getAttribute(WindFrontController::WIND_FACTORY);
		$this->windFactory->request = $request;
		$this->windFactory->response = $response;
		$this->windFactory->application = $request->getAttribute(WindFrontController::WIND_APPLICATION);
	}

	/* (non-PHPdoc)
	 * @see WindFilter::postHandle()
	 */
	public function postHandle($request = null, $response = null) {}
}

?>