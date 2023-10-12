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
    public $responseBytes;

    public function __construct($id = null)
    {
        parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
    }
    public static function recordExists($host,$port,$ip_address,$responseCode,$requestDate,$requestMethod,$query){
      $data = null;
      $results = $GLOBALS['db']
            ->database(self::DB)
            ->table(self::TABLE)
            ->select(self::PRIMARYKEY)
            ->where("host","=","'" . $host . "'")
            ->andWhere("port","=",$port)
            ->andWhere("ip_address","=","'" . $ip_address . "'")
            //->andWhere("platform","=","'" . $platform . "'")
            //->andWhere("browser","=","'" . $browser . "'")
            //->andWhere("version","=","'" . $version . "'")
            ->andWhere("responseCode","=",$responseCode)
            ->andWhere("requestDate","=","'" . $requestDate . "'")
            ->andWhere("requestMethod","=","'" . $requestMethod . "'")
            ->andWhere("query","=","'" . preg_replace("/'/","''",$query) . "'")
            //->andWhere("referrer","=","'" . $referrer . "'")
            ->get();
      if(!mysqli_num_rows($results)){
        return false;
      }
      while($row = mysqli_fetch_assoc($results)){
        $data = $row[self::PRIMARYKEY];
      }
      return $data;
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
    public static function dailyCount($date = null){
      $data = null;
      $GLOBALS['db']->database(self::DB)
                    ->table(self::TABLE)
                    ->select("count(*) as requests,cast(requestDate as date) as reqDate");
      if(!is_null($date)){
        $date = date("Y-m-d",strtotime($date));
        $GLOBALS['db']->where("cast(requestDate as date)","=","cast('" . $date . "' as date)");
      }
      $results = $GLOBALS['db']->groupBy("reqDate")->orderBy("reqDate desc")->get();
      if(!mysqli_num_rows($results) && !is_null($date)){
        throw new \Exception("No Requests for " . $date);
      }elseif(!mysqli_num_rows($results)){
        throw new \Exception("No Requests");
      }
      while($row = mysqli_fetch_assoc($results)){
        $data[] = $row;
      }
      return $data;
    }
    public static function docTypeCounts($extension){
      $data = null;
      $results = $GLOBALS['db']
          ->database(self::DB)
          ->table(self::TABLE)
          ->select("count(*) as downloads,query")
          ->where("host","=","'loe.outlawdesigns.io'")
          ->andWhere("query","like","'%" . $extension . "'")
          ->andWhere("responseCode","in","(202,206,304)")
          ->groupBy("query")
          ->orderBy("downloads desc, requestDate desc")
          ->get();
      if(!mysqli_num_rows($results)){
        throw new \Exception('No Docs');
      }
      while($row = mysqli_fetch_assoc($results)){
        $data[] = $row;
      }
      return $data;
    }
    public static function dateConstrainedSearch($key,$value,$dateOperator,$datevalue){
      $data = null;
      $results = $GLOBALS['db']
          ->database(self::DB)
          ->table(self::TABLE)
          ->select(self::PRIMARYKEY)
          ->where($key,"like","'%" . parent::cleanString($value) . "'")
          ->andWhere("requestDate",$dateOperator,"'" . $datevalue . "'")
          ->get();
      if(!mysqli_num_rows($results)){
        throw new \Exception('No Records');
      }
      while($row = mysqli_fetch_assoc($results)){
        $data[] = new self($row[self::PRIMARYKEY]);
      }
      return $data;
    }
    public static function get404s(){
      $data = null;
      $results = $GLOBALS['db']
          ->database(self::DB)
          ->table(self::TABLE)
          ->select("ip_address,count(*) as count")
          ->where("responseCode","=","404")
          ->groupBy("ip_address")
          ->get();
      if(!mysqli_num_rows($results)){
        throw new \Exception('No 404s produced!');
      }
      while($row = mysqli_fetch_assoc($results)){
        $data[] = $row;
      }
      return $data;
    }
    public static function count(){
      return parent::count(self::DB,self::TABLE);
    }
    public static function countOf($key){
      return parent::countOf(self::DB,self::TABLE,$key);
    }
    public static function getAll(){
      $data = array();
      $ids = parent::getAll(self::DB,self::TABLE,self::PRIMARYKEY);
      foreach($ids as $id){
          $data[] = new self($id);
      }
      return $data;
    }
    public static function search($key,$value){
      $data = array();
      $ids = parent::search(self::DB,self::TABLE,self::PRIMARYKEY,$key,$value);
      foreach($ids as $id){
        $data[] = new self($id);
      }
      return $data;
    }
    public static function recent($limit){
      $data = array();
      $ids = Record::getRecent(self::DB,self::TABLE,self::PRIMARYKEY,$limit);
      foreach($ids as $id){
        $data[] = new self($id);
      }
      return $data;
    }
}
