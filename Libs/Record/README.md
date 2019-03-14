# record_php

This class is an extension of <a href="https://github.com/outlawstar4761/db_php">db_php</a>. Db cleans and wraps queries while Record provides an abstract class that can be extend to represent generic database records.

## Usage

It is important to note that all public property names should correspond to their database column names.

Private or protected properties can be added and subtracted as desired.

```
require_once __DIR__ . '/record.php';

class Person extends Record{
  const DB = 'example';
  const TABLE = 'people';
  const PRIMARYKEY = 'id';
  
  public $firstName;
  public $lastName;
  public $favorite_color;
  public $isAlive;
  
  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }

}

//initialize empty and create

$p = new Person();
$p->first_name = 'Sally';
$p->last_name = 'Jones';
$p->favorite_color = 'Blue';
$p->isAlive = false;
$p->create();

//initialize empty and create 2

$data = array("first_name"=>"Sally","last_name"=>"Jones","favorite_color"=>"blue","isAlive"=>false);
$p = new Person();
$p->setFields($data)->create();

//initialize existing and update

$p = new Person($id);
$p->isAlive = $p->isAlive ? false : true;
$p->update();




```
