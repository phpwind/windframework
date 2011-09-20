<?php
Wind::import('WIND:fitler.WindHandlerInterceptor');
/**
 * action拦截器父类
 *
 * 继承实现拦截链preHandle（前置）和postHandle（后置）职责.将实现的拦截链添加到应用配置中,使之生效:
 * 例如实现formFilter,则需要在应用配置中添加如下配置:
 * <code>
 * 'filters' => array(
 * 		'class' => 'WIND:filter.WindFilterChain',	//设置使用的拦截链实现
 * 		'filter1' => array(
 * 			'class' => 'MYAPP:filter.formFilter',	//设置设置实现的formFilter类路径,MYAPP必须是一个有效的经过注册的命名空间
 * 			'pattern' => '*',	//此处设置该拦截规则应用的范围,*意味着所有的action都将会应用该拦截规则
 *     )
 *  )
 * </code>
 * 关于pattern的设置说明如下：
 * <ul>
 * <li>*：则所有的请求都将会应用该拦截器</li>
 * <li>moduleA*: 则所有配置的moduleA模块下的请求都将会应用该拦截器</li>
 * <li>moduleA_index*: 则moduleA模块下的indexController下的所有Action请求都将会应用该拦截器</li>
 * <li>moduleA_index_add*: 则module模块下的indexController下的addAction将会应用该拦截器</li>
 * </ul>
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.filter
 */
abstract class WindActionFilter extends WindHandlerInterceptor {
	/**
	 * action跳转类
	 * 
	 * @var WindForward
	 */
	protected $forward = null;
	/**
	 * 错误处理类
	 * 
	 * @var WindErrorMessage
	 */
	protected $errorMessage = null;

	/**
	 * 构造函数
	 * 
	 * 初始化类属性
	 * 
	 * @param WindForward $forward 设置当前的forward对象
	 * @param WindErrorMessage $errorMessage 设置错误处理的errorMessage
	 */
	public function __construct($forward, $errorMessage) {
		$this->forward = $forward;
		$this->errorMessage = $errorMessage;
		$args = func_get_args();
		unset($args[0], $args[1]);
		foreach ($args as $key => $value)
			property_exists(get_class($this), $key) && $this->$key = $value;
	}

	/**
	 * 设置模板数据
	 * 
	 * 此方法设置的参数,作用域仅仅只是在当前模板中可用,调用的方法为{$varName}
	 * 
	 * @param string|array|object $data 需要设置输出的参数
	 * @param string $key <pre>
	 * 参数的名字,默认为空，如果key为空，并且$data是数组或是对象的时候，则$data中的元素将会作为单独的参数保存到输出数据中
	 * </pre>
	 */
	protected function setOutput($data, $key = '') {
		$this->forward->setVars($data, $key);
	}

	/**
	 * 设置全局模板数据
	 * 
	 * 设置为Global的参数数据,将可以在所有子模板中共用,在模板中的通过{@G:varName}的方式去获取变量
	 * 
	 * @param string|array|object $data 需要设置的数据
	 * @param string $key <pre>
	 * 参数的名字,默认为空，如果key为空，并且$data是数组或是对象的时候，则$data中的元素将会作为单独的参数保存到Global数据中
	 * </pre>
	 */
	protected function setGlobal($data, $key = '') {
		Wind::getApp()->setGlobal($data, $key);
	}

	/**
	 * 从指定源中根据输入的参数名获得输入数据
	 * 
	 * @param string $name 需要获取的值的key
	 * @param string $type <pre>
	 * 获取数据源,可以是(GET POST COOKIE)中的一个,每种都将从各自的源中去获取对应的数值:
	 * <ul>
	 * <li>GET: 将从$_GET中去获取数据</li>
	 * <li>POST: 将从$_POST中去获取数据</li>
	 * <li>COOKIE: 将从$_COOKIE中去获取数据</li>
	 * <li>其他值: 将依次从request对象的attribute,$_GET,$_POST,$_COOKIE,$_REQUEST,$_ENV,$_SERVER中去尝试获取该值.</li>
	 * </ul>
	 * 该参数默认为空
	 * </pre>
	 * @param string $callback 回调函数,缺省值为空数组,该回调函数支持数组格式,即可以是调用类中的方法
	 * @return array|string <pre>
	 * 当有$callback的时候返回一个数组，其有两个元素：
	 * <ul>
	 * <li>第一个元素: 获得的用户输入的值</li>
	 * <li>第二个元素：执行$callback之后返回的值</li>
	 * </ul>
	 * </pre>
	 */
	protected function getInput($name, $type = '', $callback = array()) {
		$value = '';
		switch (strtolower($type)) {
			case IWindRequest::INPUT_TYPE_GET:
				$value = $this->getRequest()->getGet($name);
				break;
			case IWindRequest::INPUT_TYPE_POST:
				$value = $this->getRequest()->getPost($name);
				break;
			case IWindRequest::INPUT_TYPE_COOKIE:
				$value = $this->getRequest()->getCookie($name);
				break;
			default:
				$value = $this->getRequest()->getAttribute($name);
		}
		return $callback ? array($value, call_user_func_array($callback, array($value))) : $value;
	}
}
?>