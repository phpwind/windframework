<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WActionServlet {
	protected $reuqest = null;
	protected $response = null;
	
	const METHOD_DELETE = "DELETE";
	const METHOD_HEAD = "HEAD";
	const METHOD_GET = "GET";
	const METHOD_OPTIONS = "OPTIONS";
	const METHOD_POST = "POST";
	const METHOD_PUT = "PUT";
	const METHOD_TRACE = "TRACE";
	
	protected function __construct() {
		try {
			$this->reuqest = W::getInstance('WHttpRequest');
			$this->response = $this->reuqest->getResponse();
			
		} catch (Exception $exception) {
			throw new WException('init action servlet failed!!');
		}
	}
	
	public function run() {
		if ($this->reuqest === null || $this->response === null)
			throw new WException('init action servlet failed!!');
		
		$this->service($this->reuqest, $this->response);
		
		$this->response->sendResponse();
	}
	
	abstract function process($request, $resopnse);
	
	/**
	 * Receives standard HTTP requests from the public
	 * <code>service</code> method and dispatches
	 * them to the <code>do</code><i>XXX</i> methods defined in 
	 * this class.There's no need to override this method.
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function service(WHttpRequest $request, WHttpResponse $response) {
		$method = $request->getRequestMethod();
		
		if (strcasecmp($method, self::METHOD_GET) == 0) {
			$this->doGet($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_POST) == 0) {
			$this->doPost($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_PUT) == 0) {
			$this->doPut($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_DELETE) == 0) {
			$this->doDelete($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_HEAD) == 0) {

		} else if (strcasecmp($method, self::METHOD_OPTIONS) == 0) {

		} else if (strcasecmp($method, self::METHOD_TRACE) == 0) {

		} else {
			$errMsg = 'your request method is not supported!!!';
			$response->sendError(WHttpResponse::SC_METHOD_NOT_ALLOWED, $errMsg);
		}
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function doPost(WHttpRequest $request, WHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method post is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function doGet(WHttpRequest $request, WHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method get is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function doPut(WHttpRequest $request, WHttpResponse $response) {
		$this->process($request, $response);
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function doDelete(WHttpRequest $request, WHttpResponse $response) {
		$this->process($request, $response);
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	protected function doTrace(WHttpRequest $request, WHttpResponse $response) {

	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	protected function doOptions(WHttpRequest $request, WHttpResponse $response) {

	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @throws WException
	 */
	protected function doHead(WHttpRequest $request, WHttpResponse $response) {

	}

}