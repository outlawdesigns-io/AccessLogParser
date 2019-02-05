<?php

require_once __DIR__ . '/PhpUserAgent/Source/UserAgentParser.php';

class AccessLogParser{

    const IPPATTERN = '/[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}/';
    const LOCAHOSTPATTERN = '/::1/';
    const DATEPATTERN = '/\[(.*)\-[0][0-9]/';
    const METHODPATTERN = '/"([A-Z]{3,7})\s\/?/';
    const QUERYPATTERN = '/\"[A-Z]{3,7}\s(.*)HTTP/';
    const REFERRERPATTERN = '/"http:(.*)\"\w/';
    const RESPONSEPATTERN = '/HTTP\/[0-9]\.[0-9]"\s([0-9]{3})/';
    const QUOTEPATTERN = '/"([^"]*)"/';
    const LOOPBACKADDRESS = '127.0.0.1';

    public function __construct(){}

    public static function parseIP($logStr){
        if(preg_match(self::LOCAHOSTPATTERN,$logStr,$matches)){
          return self::LOOPBACKADDRESS;
        }
        if(preg_match(self::IPPATTERN,$logStr,$matches)){
            return trim($matches[0]);
        }
        return false;
    }
    public static function parseDate($logStr){
        if(preg_match(self::DATEPATTERN,$logStr,$matches)){
            $dateStr = trim($matches[1]);
            $datePieces = explode('/',$dateStr);
            $timePieces = explode(':',$datePieces[2]);
            $day = $datePieces[0];
            $month = date('m',strtotime($datePieces[1]));
            $year = $timePieces[0];
            $hour = $timePieces[1];
            $minute = $timePieces[2];
            $second = $timePieces[3];
            $dateStr = $month . '/' . $day . '/' . $year . ' ' . $hour . ':' . $minute . ':' . $second;
            return date('m/d/Y H:i:s',strtotime($dateStr));
        }
        return false;
    }
    public static function parseMethod($logStr){
        if(preg_match(self::METHODPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseQuery($logStr){
        if(preg_match(self::QUERYPATTERN,$logStr,$matches)){
            return trim(preg_replace("/\r\n|\r|\n|\||,/",'',$matches[1]));
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseReferrer($logStr){
        if(preg_match(self::REFERRERPATTERN,$logStr,$matches)){
            return "http:" . preg_replace("/\"/",'',$matches[1]);
        }
        return false;
    }
    public static function parseResponseCode($logStr){
        if(preg_match(self::RESPONSEPATTERN,$logStr,$matches)){
            return trim($matches[1]);
        }
        return false;
    }
    public static function parseUserAgent($logStr){
      $user = parse_user_agent($logStr);
      if($user['platform'] != null){
        return $user;
      }
      if(preg_match_all(self::QUOTEPATTERN,$logStr,$matches)){
        $pieces = explode('/',$matches[1][2]);
        return array('platform'=>'','browser'=>$pieces[0],'version'=>$pieces[1]);
      }
      return false;
    }
}
