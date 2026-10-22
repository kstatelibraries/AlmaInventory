<?php
//Author: Terry Brady, Georgetown University Libraries

class Alma {
  // Declared explicitly: PHP 8.2+ deprecates dynamic properties
  private string $alma_apikey;

	function __construct() {
      $configpath = parse_ini_file ("Alma.prop", false);
      $proppath = $configpath["proppath"];
      $sconfig = parse_ini_file ($proppath, false);
      $this->alma_apikey = $sconfig["ALMA_APIKEY"];
	}

  function getApiKey() {
    return $this->alma_apikey;
  }

  function getRequest($param) {
    if (isset($param["apipath"])){
      $apipath = $param["apipath"];
      unset($param["apipath"]);
      $param["apikey"] = $this->getApiKey();
      $url = "{$apipath}?" . http_build_query($param);
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
  	  ));

      $jsonstr = curl_exec ($ch);

      curl_close ($ch);
      echo $jsonstr;
    }
    echo "";
  }
// Alma PUT cURL -- added by K-State Libraries 05/2023
  function putRequest($param, $body) {
    if (isset($param["apipath"])) {
      $apipath = $param["apipath"];
      unset($param["apipath"]);
      $param["generate_description"] = "false";
      $param["apikey"] = $this->getApiKey();
      $url = "{$apipath}" .  http_build_query($param);
      $url = str_replace("apipath=", "", $url);
  
      $ch = curl_init();
  
      curl_setopt($ch, CURLOPT_URL, urldecode($url));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json'
      ));
      $response = curl_exec($ch);
      $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
      curl_close($ch);
      // print urldecode($url); // troubleshooting for json body

      // print_r($body); // troubleshooting for json body

      echo $response;
    }
    return null;

  }



}