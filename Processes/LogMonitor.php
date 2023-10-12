<?php

require __DIR__ . '/../Models/Request.php';
require __DIR__ . '/../Models/AccessLogParser.php';

class LogMonitor{

  const DEBUG = false;

  public $recordsProcessed;

  protected $_host;
  protected $_lastRequestTime;
  protected $_lines = array();

  public function __construct($hostObject){
    $this->_host = $hostObject;
    $this->recordsProcessed = 0;
    $this->_readData()
      ->_parseLines();
  }
  protected function _readData(){
    if(!$this->_lines = file($this->_host->log_path)){
      throw new \Exception('Unable to Read: ' . $this->_host->log_path);
    }
    return $this;
  }
  protected function _parseLines(){
    foreach($this->_lines as $line){
      if($ip_address = AccessLogParser::parseIP($line)){
        $request = $this->_parseRequest($ip_address,$line);
        if(!Request::recordExists($this->_host->label,$this->_host->port,$request->ip_address,$request->responseCode,$request->requestDate,$request->requestMethod,$request->query)){
          $this->_saveRequest($request);
        }
      }
    }
    return $this;
  }
  protected function _parseRequest($ip_address,$line){
    $request = new Request();
    $request->host = $this->_host->label;
    $request->port = $this->_host->port;
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
    return $request;
  }
  protected function _saveRequest($request){
    try{
      self::DEBUG ? print_r($request):$request->create();
      $this->recordsProcessed++;
    }catch(\Exception $e){
      throw new \Exception($e->getMessage());
    }
    return $this;
  }
}
