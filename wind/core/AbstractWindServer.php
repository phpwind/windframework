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
abstract class AbstractWindServer {

	const METHOD_DELETE = "DELETE";

	const METHOD_HEAD = "HEAD";

	const METHOD_GET = "GET";

	const METHOD_OPTIONS = "OPTIONS";

	const METHOD_POST = "POST";

	const METHOD_PUT = "PUT";

	const METHOD_TRACE = "TRACE";

	private $request;

	private $response;

	/**
	 * @throws Exception
	 */
	public function __construct() {
		try {
			$this->request = new WindHttpRequest();
			$this->response = $this->request->getResponse();
		} catch (Exception $exception) {
			throw new Exception('init action servlet failed!!');
		}
	}

	/**
	 * 执行操作
	 * @throws Exception
	 */
	public function run() {
		if ($this->request === null || $this->response === null) {
			throw new Exception('init action servlet failed!!');
		}
		$this->beforeProcess($this->request, $this->response);
		$this->service($this->request, $this->response);
		$this->afterProcess($this->request, $this->response);
		$this->response->sendResponse();
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function beforeProcess(WindHttpRequest $request, WindHttpResponse $response) {

	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function afterProcess(WindHttpRequest $request, WindHttpResponse $response) {

	}

	/**
	 * 执行请求的操作
	 */
	abstract protected function process(WindHttpRequest $request, WindHttpResponse $response);

	/**
	 * Receives standard HTTP requests from the public
	 * <code>service</code> method and dispatches
	 * them to the action methods defined in 
	 * this class.There's no need to override this method.
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function service(WindHttpRequest $request, WindHttpResponse $response) {
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
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $errMsg);
		}
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doPost(WindHttpRequest $request, WindHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method post is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doGet(WindHttpRequest $request, WindHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method get is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doPut(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doDelete(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function doTrace(WindHttpRequest $request, WindHttpResponse $response) {

	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function doOptions(WindHttpRequest $request, WindHttpResponse $response) {

	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doHead(WindHttpRequest $request, WindHttpResponse $response) {

	}

}