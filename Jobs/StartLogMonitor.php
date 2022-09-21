<?php

require_once __DIR__ . '/../Models/Host.php';
require_once __DIR__ . '/../Models/LogMonitorRun.php';
require_once __DIR__ . '/../Processes/LogMonitor.php';

$run = new LogMonitorRun();
$startTime = microtime(true);
$run->StartTime = date("Y-m-d H:i:s");
$run->RecordsProcessed = 0;
$hosts = Host::getAll();
foreach($hosts as $host){
  if(!$host->active){
    continue;
  }
  try{
    $m = new LogMonitor($host);
    $run->RecordsProcessed += $m->recordsProcessed;
    $run->CombinedLogSize += filesize($host->log_path);
  }catch(\Exception $e){
    echo $e->getMessage() . "\n";
    continue;
  }
}
$endTime = microtime(true);
$executionSeconds = $endTime - $startTime;
$run->Hosts = count($hosts);
$run->CombinedLogSize = 0;
$run->EndTime = date("Y-m-d H:i:s");
$run->RunTime = $executionSeconds;

$run->CombinedLogSize = ($run->CombinedLogSize / 1000) / 1000;

LogMonitorRun::DEBUG ? print_r($run):$run->create();
