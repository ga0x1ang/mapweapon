<?php

require_once("curl.php");

$URL_get_access_token = "https://api.weibo.com/oauth2/access_token";

$client_id = "client_id=";
$client_secret = "client_secret=";

$code = "code=".$_GET["code"];

$redirect_uri = "redirect_uri=";

/****************

get the access_token for further use

returns an int.

****************/

function getAccessToken()
{
    global $URL_get_access_token;
    global $client_id;
    global $client_secret;
    global $redirect_uri;
    global $code;
    $grant_type = "grant_type=authorization_code";

    $parameter = $client_id."&".$client_secret."&".$grant_type."&".$redirect_uri."&".$code;
    
    $return_value = json_decode(callAPI($URL_get_access_token, "POST", $parameter), true);
    
    $result = $return_value["access_token"];
    
    return $result;
}

$access_token = getAccessToken();

?>

<form name="send" action="./shoot.php" method="POST">
<input type="hidden" name="access_token" value="<?php echo $access_token; ?>" />
<input type="text" name="message" />
<input type="submit" value="发！" />
</form>

<form name="get" action="./targets.php" method="POST">
<input type="hidden" name="access_token" value="<?php echo $access_token; ?>" />
<input type="submit" value="拿！" />
</form>
