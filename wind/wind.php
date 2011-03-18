<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 * @version $Id$
 */

/*
 * 加载类库，并初始化核心文件
 * */
require 'WindBase.php';

class Wind extends W {

}

/* 组件定义名称 */
!defined('COMPONENT_WEBAPP') && define('COMPONENT_WEBAPP', 'windWebApp');
!defined('COMPONENT_ERRORHANDLER') && define('COMPONENT_ERRORHANDLER', 'errorHandler');
!defined('COMPONENT_LOGGER') && define('COMPONENT_LOGGER', 'windLogger');
!defined('COMPONENT_FORWARD') && define('COMPONENT_FORWARD', 'forward');
!defined('COMPONENT_ROUTER') && define('COMPONENT_ROUTER', 'urlBasedRouter');
!defined('COMPONENT_URLHELPER') && define('COMPONENT_URLHELPER', 'urlHelper');
!defined('COMPONENT_VIEW') && define('COMPONENT_VIEW', 'windView');
!defined('COMPONENT_VIEWRESOLVER') && define('COMPONENT_VIEWRESOLVER', 'viewResolver');
!defined('COMPONENT_TEMPLATE') && define('COMPONENT_TEMPLATE', 'template');
!defined('COMPONENT_ERRORMESSAGE') && define('COMPONENT_ERRORMESSAGE', 'errorMessage');
!defined('COMPONENT_DB') && define('COMPONENT_DB', 'db');

//TODO 迁移更新框架内部的常量定义到这里  配置/异常类型等 注意区分异常命名空间和类型


//********************约定变量***********************************
define('WIND_M_ERROR', 'windError');
define('WIND_CONFIG_CACHE', 'wind_components_config');

//**********配置*******通用常量定义***************************************
define('WIND_CONFIG_CONFIG', 'config');
define('WIND_CONFIG_CLASS', 'class');
define('WIND_CONFIG_CLASSPATH', 'path');
define('WIND_CONFIG_RESOURCE', 'resource');
define('WIND_CONFIG_VALUE', 'value');


//************DBsql构造中condition的条件key**********************************************************
define('WIND_DB_C_FIELD', 'field');
define('WIND_DB_C_WHERE', 'where');
define('WIND_DB_C_WHEREVALUE', 'whereValue');
define('WIND_DB_C_ORDER', 'order');
define('WIND_DB_C_LIMIT', 'limit');
define('WIND_DB_C_OFFSET', 'offset');
define('WIND_DB_C_GROUP', 'group');
define('WIND_DB_C_HAVING', 'having');
define('WIND_DB_C_HAVINGVALUE', 'havingValue');
define('WIND_DB_C_RESULTTINDEXKEY', 'resultIndexKey');

