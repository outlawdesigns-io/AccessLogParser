<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class Request extends Record{

    const DB = 'web_access';
    const TABLE = 'requests';
    const PRIMARYKEY = 'id';

    public $id;
    public $host;
    public $port;
    public $ip_address;
    public $platform;
    public $browser;
    public $version;
    public $responseCode;
    public $requestDate;
    public $requestMethod;
    public $query;
    public $referrer;

    public function __construct($id = null)
    {
        parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
    }
    public static function lastRequest($host,$port){
        $data = null;
        $results = $GLOBALS['db']
            ->database(self::DB)
            ->table(self::TABLE)
            ->select(self::PRIMARYKEY)
            ->where("host","=","'" . $host . "'")
            ->andWhere("port","=",$port)
            ->orderBy(self::PRIMARYKEY . " desc limit 1")
            ->get();
        if(!mysqli_num_rows($results)){
          throw new \Exception('No requests for host: ' . $host);
        }
        while($row = mysqli_fetch_assoc($results)){
          $data = new self($row[self::PRIMARYKEY]);
        }
        return $data;
    }
    public static function getAll(){
      $data = array();
      $ids = parent::getAll(self::DB,self::TABLE,self::PRIMARYKEY);
      foreach($ids as $id){
          $data[] = new self($id);
      }
      return $data;
    }
}
