<?php

/*
 * GCM : Google Cloud Messaging
 * info : http://developer.android.com/google/gcm/index.html
 */

class GCM {

  private $ApiKey = null;
  
  private $timeout = 60;

  function __construct($ApiKey) {
    $this->ApiKey = $ApiKey;
  }

  public function setApiKey($ApiKey) {
    $this->ApiKey = $ApiKey;
  }

  public function send($token = null, $data = null) {

   if (empty($token) || empty($data)) {
      return false;
    }
    
    $ch = curl_init();
    $opts[CURLOPT_URL] = true;
    $opts[CURLOPT_URL] = 'https://android.googleapis.com/gcm/send';
    $opts[CURLOPT_RETURNTRANSFER] = 1;
    $opts[CURLOPT_CONNECTTIMEOUT] = $this->timeout;
    $opts[CURLOPT_HTTPHEADER] = array(
        'Authorization: key='.$this->ApiKey,
        'Content-Type: application/json'
    );
    
    $payload = array(
        'registration_ids' => array($token),
        'data' => $data
    );
    
    $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
    
    curl_setopt_array($ch, $opts);
    $result = curl_exec ($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    
    //TODO: Throw Exception
    if ($status == 200) {
      
      //Success
      return json_decode($result, true);
      
    } elseif ($status == 400) {
      
      //Request Problem
      return false;
      
    } elseif ($status == 401) {
      
      //Error Authenticating the sender account
      return false;
      
    } elseif ($status > 500) {
      
      /*
       * Errors in the 500-599 range (such as 500 or 503) 
       * indicate that there wa an internal error in the GCM server 
       * while trying to process the request, or that the server is temporarily unavailable
       */
      return false;
      
    } else {
      return false;
    }
    
  }

}

