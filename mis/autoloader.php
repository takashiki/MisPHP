<?php
spl_autoload_register('loadClass');

function loadClass($class) {
  $class_file = str_replace(array('\\', '_'), '/', $class).'.php';

  $dir = dirname(__DIR__);
  $file = $dir.'/'.$class_file;
  if (file_exists($file)) {
    require $file;
    return;
  }
}