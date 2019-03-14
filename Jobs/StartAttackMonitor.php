<?php

require_once __DIR__ . '/../Processes/AttackMonitor.php';

try{
  $msgTo = "9012646875@tmomail.net";
  $authToken = AttackMonitor::authenticate($username,$password);
  $monitor = new AttackMonitor($msgTo,$authToken);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
