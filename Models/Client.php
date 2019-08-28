<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class Client extends Record{

  const DB = 'web_access';
  const TABLE = 'Client';
  const PRIMARYKEY = 'Id';

  public $Id;
  public $IpAddress;
  public $StreetAddress;
  public $City;
  public $Country;
  public $CountryCode;
  public $Isp;
  public $lat;
  public $lon;
  public $Org;
  public $Region;
  public $RegionName;
  public $TimeZone;
  public $Zip;
  public $Malevolent;

  public function __construct($Id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
  public static function exists($IpAddress){
    $results = $GLOBALS['db']
        ->database(self::DB)
        ->table(self::TABLE)
        ->select(self::PRIMARYKEY)
        ->where("IpAddress","=","'" . $IpAddress . "'")
        ->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    return true;
  }
}
