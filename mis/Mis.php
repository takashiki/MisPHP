<?php
namespace mis;

class Mis
{
  protected $request;
  
  protected $router;
  
  protected $dispacther;
  
  public $dirs = array();
  
  public $config = array();
  
  public function __construct($config) {
    $this->config($config);
    $this->loadDirs();
    spl_autoload_register(array($this, 'loadClass'));
    $this->request = new Request();
    $this->dispacther = new Dispatcher();
    set_exception_handler(array($this, 'handleException'));
  }
  
  public function run() {
    $dispacthed = $this->dispacther->dispatch($this->request);
    if(!$dispacthed) {
      echo 'false';
    }
  }
  
  public function route($pattern, $callback) {
    $this->dispacther->map($pattern, $callback);
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
  
  public function loadClass($class) {
    $class_file = str_replace(array('\\', '_'), '/', $class).'.php';

    foreach ($this->dirs as $dir) {
      $file = $dir.'/'.$class_file;
      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }
  
  public function loadDirs() {
    $this->addDir(dirname(__DIR__));
    foreach ($this->config['dirs'] as $dir) {
      $this->addDir($dir);
    }
  }
  
  public function addDir($dir) {
    if (is_array($dir) || is_object($dir)) {
      foreach ($dir as $value) {
        $this->addDir($value);
      }
    } else if (is_string($dir)) {
      if (!in_array($dir, $this->dirs)) $this->dirs[] = $dir;
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
}