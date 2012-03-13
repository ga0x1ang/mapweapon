<?php

/************************
  packed cURL calls

  usually returns a json

  $url: the url of the API
  $type: "GET" or "POST"
  $parameter: the values send to the API when accessing it

 ************************/

function callAPI($url, $type, $parameter)
{
    if($type == "GET")
    {
        $url = $url.'?'.$parameter;
    }

    $curl = curl_init($url);
    
    if($type == "POST")
    {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameter);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
