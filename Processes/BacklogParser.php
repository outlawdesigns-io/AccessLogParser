<?php

require_once __DIR__ . '/../Models/AccessLogParser.php';
require_once __DIR__ . '/../Models/Request.php';

class BacklogParser{
  const DEBUG = false;
  protected $_logPath;
  protected $_hostName;
  protected $_port;
  protected $_timeAdjustment;
  protected $_lines = array();
  protected $foundRecords = array();

  public function __construct($hostname,$port,$logPath,$timeAdjustment){
    $this->_hostName = $hostname;
    $this->_port = $port;
    $this->_logPath = $logPath;
    $this->_timeAdjustment = $timeAdjustment;
    $this->_readData()->_parseLines();
  }
  protected function _readData(){
    if(!$this->_lines = file($this->_logPath)){
      throw new \Exception('Unable to Read: ' . $this->_logPath);
    }
    return $this;
  }
  protected function _parseLines(){
    $countedRecords = 0;
    $foundRecords = 0;
    $totalRecords = count($this->_lines);
    $startTime = microtime(true);
    // rsort($this->_lines);
    foreach($this->_lines as $line){
      if($ip_address = AccessLogParser::parseIP($line)){
        ++$countedRecords;
        $parsed = $this->_populateDefaults($this->_parseRequest($ip_address,$line));
        if(!$id = Request::recordExists($this->_hostName,$this->_port,$parsed->ip_address,$parsed->responseCode,$parsed->requestDate,$parsed->requestMethod,$parsed->query)){
          print_r($parsed);
          echo ++$foundRecords . " / " . $countedRecords . " / " . $totalRecords . "\n";
          echo "Execution Time: " . $this->_formatTime(microtime(true) - $startTime) . "\n";
          if(!self::DEBUG){
            $parsed->create();
          }
        }else if(!self::DEBUG){
          $recordToUpdate = new Request($id);
          $recordToUpdate->responseBytes = $parsed->responseBytes;
          $recordToUpdate->update();
        }
      }
    }
    return $this;
  }
  protected function _populateDefaults($request){
    if(is_null($request->host) || empty($request->host)){
      $request->host = 'NA';
    }
    if(is_null($request->port) || empty($request->port)){
      $request->port = 9999;
    }
    if(is_null($request->ip_address) || empty($request->ip_address)){
      $request->ip_address = '255.255.255.255';
    }
    if(is_null($request->platform) || empty($request->platform)){
      $request->platform = 'NA';
    }
    if(is_null($request->browser) || empty($request->browser)){
      $request->browser = 'NA';
    }
    if(is_null($request->version) || empty($request->version)){
      $request->version = 'NA';
    }
    if(is_null($request->responseCode) || empty($request->responseCode)){
      $request->responseCode = 999;
    }
    if(is_null($request->requestMethod) || empty($request->requestMethod)){
      $request->requestMethod = 'KILL';
    }
    if(is_null($request->referrer) || empty($request->referrer)){
      $request->referrer = 'NA';
    }
    return $request;
  }
  protected function _parseRequest($ip_address,$line){
    $request = new Request();
    $request->host = $this->_hostName;
    $request->port = $this->_port;
    $request->ip_address = $ip_address;
    $request->requestDate = date('Y/m/d H:i:s',strtotime(AccessLogParser::parseDate($line) . ' ' . $this->_timeAdjustment));
    $request->requestMethod = AccessLogParser::parseMethod($line);
    $request->query = AccessLogParser::parseQuery($line);
    $request->referrer = AccessLogParser::parseReferrer($line);
    $request->responseCode = AccessLogParser::parseResponseCode($line);
    $request->responseBytes = AccessLogParser::parseBytesSent($line);
    if($user = AccessLogParser::parseUserAgent($line)){
      $request->platform = $user['platform'];
      $request->browser = $user['browser'];
      $request->version = $user['version'];
    }
    return $request;
  }
  protected function _writeToFile($lines){
    //todo write a subset of lines to a file to be imported later
  }
  protected function _importFile($lines){
    //import a set of lines.
  }
  protected function _formatTime($seconds_input){
    $hours = (int)(($minutes = (int)($seconds = (int)($milliseconds = (int)($seconds_input * 1000)) / 1000) / 60) / 60);
    return $hours.':'.($minutes%60).':'.($seconds%60).(($milliseconds===0)?'':'.'.rtrim($milliseconds%1000, '0'));
  }
}
