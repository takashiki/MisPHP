<?php
// +----------------------------------------------------------------------
// | MisPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://qianxun.us All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 天心流水 <857995137@qq.com>
// +----------------------------------------------------------------------

//----------------------------------
// MisPHP公共入口文件
//----------------------------------

//需要php版本大于5.3.9
if(version_compare(PHP_VERSION,'5.3.9','<'))  die('require PHP >= 5.3.9 !');

//版本信息
const MIS_VER = 0.1;

// 系统常量定义
defined('MIS_PATH') 	or define('MIS_PATH',     __DIR__.'/');
defined('APP_PATH') 	or define('APP_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_DEBUG') 	or define('APP_DEBUG',      false); // 是否调试模式

//系统信息
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

//启动项目
require MIS_PATH . 'common/functions.php';

if (!spl_autoload_register('mis_autoload'))
  die('register auto load class function failed');

Mis\Mis::run();