<?php

require __DIR__ . '/../Models/Request.php';
require __DIR__ . '/../Models/Client.php';

class ClientBuilder{

  const LOOPBACK = '127.0.0.1';
  const LOCAL = '172.17.0.1';
  const LOCALPATT1 = '/192\.168\.1/';
  const IPKEY = 'ip_address';
  const IPPATT = '/[1-9]{1,3}\.[1-9]{1,3}\.[1-9]{1,3}\.[1-9]{1,3}/';
  const IPAPI = 'http://ip-api.com/json/';
  const QUERYLIMIT = 40;

  public $localIp;
  public $newClients = array();
  public $quotaExcess = array();

  public function __construct(){
    $this->localIp = $this->getLocalIp();
    $this->_getClients();
  }
  protected function _getClients(){
    $ipList = Request::browse(Request::DB,Request::TABLE,self::IPKEY);
    $counter = 0;
    foreach($ipList as $ip){
      if(preg_match(self::IPPATT,$ip) && !$this->isLocalRequest($ip) && !Client::exists($ip)){
        if($counter++ <= self::QUERYLIMIT){
          $this->newClients[] = $this->_buildNewClient($ip);
        }else{
          $this->quotaExcess[] = $ip;
        }
      }
    }
    return $this;
  }
  protected function _buildNewClient($ip){
    $ipData = $this->getIpData($ip);
    $newClient = new Client();
    $newClient->IpAddress = $ip;
    $newClient->StreetAddress = $ipData->as;
    $newClient->City = $ipData->city;
    $newClient->Country = $ipData->country;
    $newClient->CountryCode = $ipData->countryCode;
    $newClient->Isp = $ipData->isp;
    $newClient->Lat = $ipData->lat;
    $newClient->Lon = $ipData->lon;
    $newClient->Org = $ipData->org;
    $newClient->Region = $ipData->region;
    $newClient->RegionName = $ipData->regionName;
    $newClient->TimeZone = $ipData->timezone;
    $newClient->Zip = $ipData->zip;
    return $newClient;
  }
  public function save(){
    foreach($this->newClients as $client){
      $client->create();
    }
    return $this;
  }
  public static function getLocalIp(){
    return gethostbyname(gethostname());
  }
  public static function isLocalRequest($remoteIp){
    $localIp = self::getLocalIp();
    if($remoteIp == self::LOOPBACK || $remoteIp == self::LOCAL || preg_match(self::LOCALPATT1,$remoteIp)){
      return true;
    }
    $localPieces = explode('.',$localIp);
    $remotePieces = explode('.',$remoteIp);
    if($localPieces[0] == $remotePieces[0] && $localPieces[1] == $remotePieces[1] && $localPieces[2] == $remotePieces[2]){
      return true;
    }
    return false;
  }
  public static function getIpData($ip){
    $response =  json_decode(file_get_contents(self::IPAPI . $ip));
    if($response->status == 'fail'){
      throw new \Exception($response->message);
    }
    return $response;
  }
}
