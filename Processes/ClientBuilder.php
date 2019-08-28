<?php

require __DIR__ . '/../Models/Request.php';
require __DIR__ . '/../Models/Client.php';

class ClientBuilder{

  const IPKEY = 'ip_address';

  public function __construct(){

  }
  protected function _getClients(){
    print_r(Request::browse(Request::DB,Request::TABLE,self::IPKEY));
    return $this;
  }
}
