<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class LogMonitorRun extends Record{

  const DB = 'web_access';
  const TABLE = 'LogMonitorRun';
  const PRIMARYKEY = 'Id';

  public $Id;
  public $StartTime;
  public $EndTime;
  public $RunTime;
  public $CombinedLogSize;
  public $Hosts;

  public function __construct($Id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$Id);
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
  public static function count(){
    return parent::count(self::DB,self::TABLE);
  }
  public static function countOf($key){
    return parent::countOf(self::DB,self::TABLE,$key);
  }
}
