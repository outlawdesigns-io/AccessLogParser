<?php

require __DIR__ . '/../Models/Request.php';
require __DIR__ . '/../Models/Client.php';

class ClientBuilder{

  const IPKEY = 'ip_address';

  public function __construct(){
    $this->_getClients();

  }
  protected function _getClients(){
    print_r(Request::browse(Request::DB,Request::TABLE,self::IPKEY));
    return $this;
  }
}
