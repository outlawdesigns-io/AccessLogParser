<?php

require __DIR__ . '/../models/Request.php';
require __DIR__ . '/../models/Host.php';
require __DIR__ . '/../models/AccessLogParser.php';

class LogMonitor{

  protected $_hosts = array();

  public function __construct($hostObjects){
    $this->_hosts = $hostObjects;
    $this->_parse();
  }
  protected function _parse(){
    foreach($this->_hosts as $host){
      if(!$lines = file($host->logPath)){
        throw new \Exception('Unable to Read: ' . $host->logPath);
      }
      foreach($lines as $line){
        if(!$ip_address = AccessLogParser::parseIP($line)){
          continue;
        }else{
          $request = new Request();
          $request->host = $host->label;
          $request->port = $host->port;
          $request->ip_address = $ip_address;
          $request->requestDate = AccessLogParser::parseDate($line);
          $request->requestMethod = AccessLogParser::parseMethod($line);
          $request->query = AccessLogParser::parseQuery($line);
          $request->referrer = AccessLogParser::parseReferrer($line);
          $request->responseCode = AccessLogParser::parseResponseCode($line);
          if($user = AccessLogParser::parseUserAgent($line)){
            $request->platform = $user['platform'];
            $request->browser = $user['browser'];
            $request->version = $user['version'];
          }
          $this->_validateRequest($request);
        }
      }
    }
    return $this;
  }
  protected function _validateRequest($request){
    try{
      $lastRequest = Request::lastRequest($request->host,$request->port);
      if(strtotime($request->requestDate) > strtotime($lastRequest->requestDate)){
        $request->create();
      }
    }catch(\Exception $e){
      $request->create();
    }
    return $this;
  }
}
