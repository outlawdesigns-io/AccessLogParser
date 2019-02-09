<?php

require __DIR__ . '/../models/Request.php';

class AttackMonitor{

  protected $hosts = array();
  protected $counts = array();

  public function __construct(){
    $this->_get404s();
    echo $this->_stdv($this->counts) . "\n";
    echo $this->_stdv($this->counts) * 1.96 . "\n";
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
      $this->hosts[] = $row['ip_address'];
      $this->counts[] = $row['count'];
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
