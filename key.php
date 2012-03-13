<?php

require_once("curl.php");

/****************

  get the access_token for further use

  returns an int.

 ****************/

function getAccessToken()
{
    $url = "https://api.weibo.com/oauth2/access_token";
    $client_secret = "client_secret=8fb1563f1366a12f8f21418f01e07acf";
    $client_id = "client_id=1629659974";
    $code = "code=".$_GET["code"];
    $redirect_uri = "redirect_uri=http://mapweapon.sinaapp.com/mapweapon.php";
    $grant_type = "grant_type=authorization_code";

    $parameter = $client_id."&".$client_secret."&".$grant_type."&".$redirect_uri."&".$code;

    $return_value = json_decode(callAPI($url, "POST", $parameter), true);

    $result = $return_value["access_token"];

    return $result;
}

$access_token = getAccessToken();

function getCurrentUserUid()
{
    global $access_token;
    $URL_get_uid = "https://api.weibo.com/2/account/get_uid.json";
    $parameter = 'access_token='.$access_token;

    $user_uid = json_decode(callAPI($URL_get_uid, "GET", $parameter), true);

    $result = $user_uid["uid"];

    return $result;
}

$uid = getCurrentUserUid();

?>

<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<title>某科学的超地图炮</title>
</head>
<body>
<form name="send" action="./shoot.php" method="POST">
<input type="hidden" name="access_token" value="<?php echo $access_token; ?>" />
<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
<input type="text" name="message" />
<input type="submit" value="发射" />
</form>
<br />
<form name="manage" action="./cp.php" method="POST">
<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
<input type="submit" value="管理" />
</form>
</body>
</html>
