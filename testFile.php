<?php

require_once __DIR__ . '/LogMonitor.php';


try{
    print_r(Request::lastRequest('api.outlawdesigns.io',80));
}catch(\Exception $e){
    echo $e->getMessage() . "\n";
}
exit;

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

try{
  $m = new LogMonitor($hosts);
}catch(\Exception $e){
  echo $e->getMessage() . "\n";
}
