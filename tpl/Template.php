<?php
namespace mis\tpl;

use Exception;

class Template
{
  public $view_dir;
  
  public $cache_dir;
  
  private $yields;
  
  public function __construct($dirs) {
    $this->view_dir = $dirs['view'];
    $this->cache_dir = $dirs['cache'];
  }
  
  public function render($view, $data, $toString = false) {
    $view_file = $this->view_dir . DIRECTORY_SEPARATOR . $view . '.php';
    $compile_file = $this->compile($view_file);
    
    extract($data);
    ob_start();
    include $compile_file;
    $html = ob_get_clean();
    
    if ($toString === false) {
      echo $html;
    } else {
      return $html;
    }
  }
  
  public function fetch($file) {
    if (! is_file($file)) {
      throw new Exception($file . "模版文件不存在！");
    }
    
    return file_get_contents($file);
  }
  
  public function compile($tpl) {
    if (is_file($tpl)) {
      $tpl = $this->fetch($tpl);
    } 
    
    $compile_file = $this->cache_dir . DIRECTORY_SEPARATOR . md5($tpl) . '.rtpl';
    if (! is_file($compile_file)) { 
      $code = $this->compileString($tpl);
      file_put_contents($compile_file, $code);
      file_put_contents('saekv://config.php', $code);
    }
    
    return $compile_file;
  }
  
  public function compileString($tpl) {
    $code = preg_replace_callback('/{yield=(?P<yield>\w+)}/U',
      array($this, 'replaceYield'),
      $tpl
    );
    
    preg_match('/{extends=(?P<extends>\S+)}/U', $tpl, $extends);
    if (isset($extends['extends'])) {
      preg_match_all('/{section=(?P<section>\w+)}(?P<content>.+){\/section}/isU', $tpl, $sections);
      
      for ($i = 0; $i < sizeof($sections['section']); $i++) {
        $this->yields[$sections['section'][$i]] = $sections['content'][$i];
      }
      
      $extendsFile = $this->view_dir . DIRECTORY_SEPARATOR . $extends['extends'] . '.php';
      $extendsTpl = $this->fetch($extendsFile);
      $code = $this->compileString($extendsTpl);
    }
    
    return $code;
  }
    
  public function replaceYield($matches) {
    return $this->yields[$matches['yield']];
  }
}