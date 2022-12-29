<?php

use Curl\Curl;

function CallAPI($method, $token, $url, $data = [])
{
  
    $curl = new Curl();
  
    $curl->setHeader('Content-Type', 'application/json');
    
    if($token != "") {
      $login_password=explode(":", base64_decode($token));
      $curl->setBasicAuthentication($login_password[0], $login_password[1]);
    }

    switch ($method)
    {
        case "POST":
            $curl->post($url, $data);
            break;
        case "PUT":
            $curl->put($url, $data);
            break;
        default:
            $curl->get($url);
    }

    if ($curl->error) {
      error_log('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
    } else {
        return $curl->response;
    }
}

function check_auth($username, $password)
{
  global $global;
  $token = base64_encode($username.":".$password);

  //echo $global['api_url']."/accounts/".$username; echo "=="; echo "$token"; 

  $req_check_auth = CallAPI("GET", $token, $global['api_url']."/accounts/".$username);
  return ! empty($req_check_auth);
}




?>

