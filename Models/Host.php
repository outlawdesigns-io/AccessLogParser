<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class Host extends Record{

  const DB = 'web_access';
  const TABLE = 'hosts';
  const PRIMARYKEY = 'id';

  public $id;
  public $label;
  public $port;
  public $log_path;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
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
