<?php

require __DIR__ . '/../models/Request.php';
require __DIR__ . '/../MessageClient/MessageClient.php';

class AttackMonitor extends MessageClient{

  const CONFINT = 1.64;
  const MSGNAME = 'http_404_probe';

  protected $_hosts = array();
  protected $_counts = array();
  protected $_username;
  protected $_password;
  protected $_authToken;

  public function __construct($msgTo = null,$authToken = null){
    $this->_msgTo = $msgTo;
    $this->_authToken = $authToken;
    $this->_get404s()
         ->_parse();
  }
  protected function _get404s(){
    $results = $GLOBALS['db']
                  ->database(Request::DB)
                  ->table(Request::TABLE)
                  ->select("ip_address,count(*) as count")
                  ->where("responseCode","=","404 group by ip_address")
                  ->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('No Errors to Monitor!');
    }
    while($row = mysqli_fetch_assoc($results)){
      $this->_hosts[] = $row['ip_address'];
      $this->_counts[] = $row['count'];
    }
    return $this;
  }
  protected function _parse(){
    $fatality = $this->_stdv($this->_counts) * self::CONFINT;
    for($i = 0; $i < count($this->_hosts); $i++){
      if($this->_counts[$i] >= $fatality && !self::isSent(self::MSGNAME,$this->_hosts[$i],$this->_authToken)){
        try{
          self::send($this->_buildMsg($this->_hosts[$i],$this->_counts[$i]),$this->_authToken);
        }catch(\Exception $e){
          throw new \Exception($e->getMessage());
        }
      }
    }
    return $this;
  }
  protected function _mean($values){
    return array_sum($values) / count($values);
  }
  protected function _stdv($values){
    $squares = array();
    $mean = $this->_mean($values);
    foreach($values as $value){
      $squares[] = pow($value-$mean,2);
    }
    return sqrt($this->_mean($squares));
  }
  protected function _buildMsg($host,$count){
    return array(
      "to"=>array($this->_msgTo),
      "body"=>"ATTN malicious activity warning:\nIp address: " . $host . "\nhas generated: " . $count . " 404s.",
      "flag"=>$host,
      "msg_name"=>self::MSGNAME,
      "sent_by"=>"LOE3:" . __FILE__
    );
  }
}

/*
standard deviation of 404s

get mean [average] number of 404s


*/
