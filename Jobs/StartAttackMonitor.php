<?php

require_once __DIR__ . '/../Processes/AttackMonitor.php';

try{
  $authToken = AttackMonitor::authenticate($username,$password)->token;
  $monitor = new AttackMonitor($msgTo,$authToken);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
