<?php
namespace mis;

class Request
{
  public $base;
  
  public $url;

  public $method;
  
  public function __construct() {
    $this->base = str_replace(array('\\',' '), array('/','%20'), dirname($this->getVar('SCRIPT_NAME')));
    $this->url = $this->getUrl();
    $this->method = $this->getMethod();
  }
  
  private function getVar($var, $default='') {
    return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
  }
  
  private function getMethod() {
    if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
      return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
    }
    elseif (isset($_REQUEST['_method'])) {
      return $_REQUEST['_method'];
    }

    return $this->getVar('REQUEST_METHOD', 'GET');
  }
  
  private function getUrl() {
    $url = $this->getVar('REQUEST_URI', '/');
    if ($this->base != '/' && strlen($this->base) > 0 && strpos($url, $this->base) === 0) {
      $url = substr($url, strlen($this->base));
    }
    
    return $url;
  }
}