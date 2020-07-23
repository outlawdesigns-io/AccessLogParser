<?php

require_once __DIR__ . '/../Processes/AttackMonitor.php';
require_once __DIR__ . '/AccountCredentials.php';

try{
  $authToken = AttackMonitor::authenticate($username,$password)->token;
  $monitor = new AttackMonitor($msgTo,$authToken);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
