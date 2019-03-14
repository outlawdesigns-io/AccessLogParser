<?php

require_once __DIR__ . '/../Models/Host.php';
require_once __DIR__ . '/../Processes/LogMonitor.php';

$hosts = Host::getAll();

while(true){
  try{
    $m = new LogMonitor($hosts);
  }catch(\Exception $e){
    echo $e->getMessage() . "\n";
  }
  sleep(30);
}
