<?php
namespace mis;

class Mis
{
  protected $request;
  
  protected $router;
  
  protected $dispacther;
  
  public $config = array();
  
  public $events = array();
  
  public function __construct() {
    $this->config = Config::get('app');
    set_exception_handler(array($this, 'handleException'));
    $this->request = new Request();
    $this->dispacther = new Dispatcher();
    $this->loadEvents();
    Conn::init(Config::get('db'));
  }
  
  public function run() {
    $dispacthed = $this->dispacther->dispatch($this->request);
    if (!$dispacthed) {
      $this->dispacther->execute($this->events['notFound']);
    }
  }
  
  public function route($pattern, $callback) {
    if (array_key_exists($pattern, $this->events)) {
      $this->events[$pattern] = $callback;
    } else {
      $this->dispacther->map($pattern, $callback);
    }
  }
  
  public function handleException(\Exception $e) {
    if ($this->config['debug'] !== false) {
      $traces = $e->getTrace();
      echo '<h1>Exception</h1>';
      echo '<div>'.$e->getMessage().'</div>';
      foreach ($traces as $trace) {
        echo '<ul>';
        echo '<li>File: '.$trace['file'].'</li>';
        echo '<li>Line: '.$trace['line'].'</li>';
        echo '<li>In function "'.$trace['function'].'" of class "'.$trace['class'].'"</li>';
        echo '</ul>';
      }
    } else {
      echo '<h1>Exception</h1>';
      echo '<div>Try to contact the webmaster.</div>';
    }
  }
  
  public function config($name, $value = null)
  {
    if (is_array($name)) {
      if (true === $value) {
        $this->config = array_merge_recursive($this->config, $name);
      } else {
        $this->config = array_merge($this->config, $name);
      }
    } elseif (func_num_args() === 1) {
      return isset($this->config[$name]) ? $this->config[$name] : null;
    } else {
      $this->config[$name] = $value;
    }
  }
  
  public function loadEvents() {
    $this->events['notFound'] = function() {
      echo 'default 404';
    };
  }
}