<?php

require_once __DIR__ . '/../Processes/ClientBuilder.php';

$runAgain = true;
$sleepTime = 180;

while($runAgain){
  try{
    $processor = new ClientBuilder()
    $processor->save();
  }catch(\Exception $e){
    echo $e->getMessage() . "\n";
    exit;
  }
  if(!count($processor->quotaExcess)){
    $runAgain = false;
  }else{
    echo count($processor->quotaExcess) . " clients remaining\n";
    sleep($sleepTime);
  }
}
