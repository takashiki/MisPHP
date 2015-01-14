<?php
use mis\Controller;

class Home extends Controller
{
  public function index() {
    echo 'welcome seikai';
    $this->m = new UserModel('user');
    print_r($this->m->all());
  }
}