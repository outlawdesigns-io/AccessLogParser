<?php

abstract class MessageClient{

  const MSGEND = 'http://api.attlocal.net:9667/';

  public static function authenticate($username,$password){
    $headers = array('request_token: ' . $username,'password: ' . $password);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,self::MSGEND . "authenticate");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $output = json_decode(curl_exec($ch));
    curl_close($ch);
    if(isset($output->error)){
      throw new \Exception($output->error);
    }
    return $output;
  }
  public static function verifyToken($token){
    $headers = array('auth_token: ' . $token);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,self::MSGEND . "verify");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $output = json_decode(curl_exec($ch));
    curl_close($ch);
    if(isset($output->error)){
      throw new \Exception($output->error);
    }
    return $output;
  }
  public static function send($message,$token){
    $headers = array('auth_token: ' . $token);
    $post = "message=" . base64_encode(serialize($message));
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,self::MSGEND . "send");
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }
  public static function isSent($msg_name,$flag,$token){
    $headers = array('auth_token: ' . $token);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,self::MSGEND . "sent");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }
}
