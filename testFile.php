<?php


require_once __DIR__ . '/processes/AttackMonitor.php';

try{
  $a = new AttackMonitor();
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
