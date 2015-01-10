<?php
namespace mis;

class Mis
{
  protected $request;
  
  protected $router;
  
  protected $dispacther;
  
  public function __construct() {
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
}