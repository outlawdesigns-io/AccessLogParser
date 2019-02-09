<?php


require_once __DIR__ . '/processes/AttackMonitor.php';

try{
  $a = new AttackMonitor();
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}

exit;

require_once __DIR__ . '/SendMessage/SendMessage.php';

$message = array(
  "to"=>array("outlawstar4761@gmail.com"),
  "subject"=>"Test Message",
  "body"=>"Hello world."
);

try{
  $m = new SendMessage($message);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}

exit;

require_once __DIR__ . '/LogMonitor.php';

$files = array(
  array("host"=>"162.234.44.5","port"=>80,"log"=>"/var/log/apache2/access.log"),
  array("host"=>"api.outlawdesigns.io","port"=>80,"log"=>"/var/log/apache2/api.outlawdesigns.access.log"),
  array("host"=>"loe.outlawdesigns.io","port"=>80,"log"=>"/var/log/apache2/loe.outlawdesigns.access.log"),
  array("host"=>"outlawdesigns.io","port"=>80,"log"=>"/var/log/apache2/outlawdesigns.access.log"),
  array("host"=>"api.outlawdesigns.io","port"=>8663,"log"=>"/var/www/html/log/buddy.api.log")
);

$hosts = array();

foreach($files as $file){
  $host = new Host();
  $host->label = $file['host'];
  $host->port = $file['port'];
  $host->logPath = $file['log'];
  $hosts[] = $host;
}

while(true){
  try{
    $m = new LogMonitor($hosts);
  }catch(\Exception $e){
    echo $e->getMessage() . "\n";
  }
  sleep(30);
}
