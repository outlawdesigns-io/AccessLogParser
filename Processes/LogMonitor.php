<?php

require __DIR__ . '/../Models/Request.php';
require __DIR__ . '/../Models/AccessLogParser.php';

class LogMonitor{

  protected $_hosts = array();
  protected $_lastRequest;

  public function __construct($hostObjects){
    $this->_hosts = $hostObjects;
    $this->_parse();
  }
  protected function _parse(){
    foreach($this->_hosts as $host){
      $this->_lastRequest = Request::lastRequest($host->label,$host->port);
      if(!$lines = file($host->log_path)){
        throw new \Exception('Unable to Read: ' . $host->log_path);
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
      //$lastRequest = Request::lastRequest($request->host,$request->port);
      if(strtotime($request->requestDate) > strtotime($this->_lastRequest->requestDate)){
        $request->create();
      }
    }catch(\Exception $e){
      $request->create();
    }
    return $this;
  }
}
