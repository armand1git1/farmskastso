<?php

function CallAPI($method, $token, $url, $data = false)
{
    $curl = curl_init();
    //echo $method;  echo "=="; echo $token; echo "==";  echo $url; echo "=="; print_r($data) ;
    //echo "<br />";
    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    if($token != "") {
      $headers = array(
          'Content-Type:application/json',
          'Authorization: Basic '. $token
      );
    } else {
      $headers = array(
          'Content-Type:application/json'
      );
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
    //echo $result; 
    //die();

    $info = curl_getinfo($curl);
    curl_close($curl);

    if($info['http_code'] < 400) {
      return $result;
    } else {
      return null;
    }
}

function check_auth($username, $password)
{
  global $global;
  $token = base64_encode($username.":".$password);
  $req_check_auth = CallAPI("GET", $token, $global['api_url']."/accounts/".$username);
  return ! empty($req_check_auth);
}
?>
