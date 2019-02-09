<?php

require __DIR__ . '/../models/Request.php';

class AttackMonitor{

  protected $_hosts = array();
  protected $_counts = array();

  public function __construct(){
    $this->_get404s();
    $fatality = $this->_stdv($this->_counts) * 1.96;
    for($i = 0; $i < count($this->_hosts); $i++){
      if($this->_counts[$i] >= $fatality){
        echo $this->_hosts[$i] . " -> " . $this->counts[$i] . "\n";
      }
    }
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
}

/*
standard deviation of 404s

get mean [average] number of 404s


*/
