<?php
namespace mis;

class Dispatcher
{
  protected $routes = array();
  
  public $dispatched;
  
  public function dispatch(Request $request) {    
    foreach($this->routes as $route) {
      if ($route !== false && $route->matchMethod($request->method) && $route->matchUrl($request->url)) {
        $this->execute($route->callback, $route->params);
        return true;
      }
    }
    
    $segs = explode('/', trim($request->url, '/'));
    $class = isset($segs[0]) ? $segs[0] : 'home';
    $method = isset($segs[1]) ? $segs[1] : 'index';
    if(!class_exists($class) || !method_exists($class, $method)) {
      return false;
    } else {
      $this->execute($class . '->' . $method, array_slice($segs, 2));
      return true;
    }
    
    return false;
  }
  
  public function map($pattern, $callback) {
    $url = $pattern;
    $methods = array('*');

    if (strpos($pattern, ' ') !== false) {
      list($method, $url) = explode(' ', trim($pattern), 2);

      $methods = explode('|', $method);
    }
    
    $this->routes[] = new Router($url, $callback, $methods);
  }
  
  public function execute($callback, $params) {
    $params = $params ?: array();
    $params = array_values($params);

    if(is_string($callback)) {
      if(stripos($callback, '->')) {
        list($class, $method) = explode('->', $callback);
        $instance = new $class();
        $callback = array(&$instance, $method);
      } else if (stripos($callback, '::')) {
        $callback = explode('::', $callback);
      }
    }
    
    if (is_callable($callback)) {
      call_user_func_array($callback, $params);
    } else {
      throw new \Exception('Invalid callback specified');
    }
  }
}