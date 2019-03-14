<?php

require_once __DIR__ . '/../Processes/AttackMonitor.php';

try{
  $token = AttackMonitor::authenticate($username,$password);
  $a = new AttackMonitor($msgTo,$authToken);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
