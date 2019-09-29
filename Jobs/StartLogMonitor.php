<?php

require_once __DIR__ . '/../Models/Host.php';
require_once __DIR__ . '/../Models/LogMonitorRun.php';
require_once __DIR__ . '/../Processes/LogMonitor.php';

$run = new LogMonitorRun();
$startTime = microtime(true);
$run->StartTime = date("Y-m-d H:i:s");

$hosts = Host::getAll();
try{
  $m = new LogMonitor($hosts);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
$endTime = microtime(true);
$executionSeconds = $endTime - $startTime;
$run->Hosts = count($hosts);
$run->CombinedLogSize = 0;
$run->EndTime = date("Y-m-d H:i:s");
$run->RunTime = $executionSeconds;

foreach($hosts as $host){
  $run->CombinedLogSize += filesize($host->log_path);
}
$run->CombinedLogSize = ($run->CombinedLogSize / 1000) / 1000;
$run->create();
