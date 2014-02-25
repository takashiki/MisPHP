<?php
function mis_autoload($class) {
  if(strpos($class, '\\')) {
    $name = strstr($class , '\\', true);
    if(in_array($name, array('Mis'))) {
      $path = MIS_PATH . 'core/';
    } else {
      $path = APP_PATH;
    }
    $filename = $path . trim(strstr($class , '\\'), '\\') . '.class.php';
    import($filename);
  } else {
    if(strpos($class, 'Model')) {
      $path = APP_PATH . 'model/';
    } elseif(strpos($class, 'Controller')) {
      $path = APP_PATH . 'controller/';
    }
    $filename = $path . $class . '.class.php';
    import($filename);
  }
}

function import($file) {
  static $_files = array();
  if(isset($_files[$file])) {
    return true;
  } else {
    require_cache($file);
    $_files[$file] = true;
  }
  return false;
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
  static $_importFiles = array();
  if (!isset($_importFiles[$filename])) {
    if (file_exists_case($filename)) {
      require $filename;
      $_importFiles[$filename] = true;
    } else {
      $_importFiles[$filename] = false;
    }
  }
  return $_importFiles[$filename];
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
  if (is_file($filename)) {
    if (IS_WIN && APP_DEBUG) {
      if (basename(realpath($filename)) != basename($filename)) {
        return false;
      }
    }
    return true;
  }
  return false;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
  //多行URL地址支持
  $url        = str_replace(array("\n", "\r"), '', $url);
  if (empty($msg)) {
    $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
  }
  if (!headers_sent()) {
    // redirect
    if (0 === $time) {
      header('Location: ' . $url);
    } else {
      header("refresh:{$time};url={$url}");
      echo($msg);
    }
    exit();
  } else {
    $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
    if ($time != 0) {
      $str .= $msg;
    }
    exit($str);
  }
}

/**
 * 设置和获取统计数据
 * 使用方法:
 * <code>
 * N('db',1); // 记录数据库操作次数
 * N('read',1); // 记录读取次数
 * echo N('db'); // 获取当前页面数据库的所有操作次数
 * echo N('read'); // 获取当前页面读取次数
 * </code>
 * @param string $key 标识位置
 * @param integer $step 步进值
 * @return mixed
 */
function N($key, $step=0,$save=false) {
    static $_num    = array();
    if (!isset($_num[$key])) {
        $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
    }
    if (empty($step))
        return $_num[$key];
    else
        $_num[$key] = $_num[$key] + (int) $step;
    if(false !== $save){ // 保存结果
        S('N_'.$key,$_num[$key],$save);
    }
}