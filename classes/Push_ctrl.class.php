<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Push_ctrl
{
  public static function push($to = "dvYHBgo-SVyn38DjOf7pqd:APA91bFpVsP3lgM6z-CInDww8y8xblkwEl19L6TuODrZSwmy1vxkJSzy2GvakAGYYB4c5xRp4uzBgmMOkCQEt1rxxnMHpdDnkBERlZ4pYN7IHmhOVLyw9xVXdjQ_LsCc3lg0oxCSD4SV", $notification  = array('title'=>'','body'=>''))
  {
    $client = new Client();
    $headers = [
      'Authorization' => FIREBASE_TOKEN,
      'Content-Type' => 'application/json'
    ];
    $bodydata = array(
      'to' => $to,
      'notification' => $notification
    );
    $body = json_encode($bodydata);
    $request = new Request('POST', 'https://fcm.googleapis.com/fcm/send', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody();
  }
}
