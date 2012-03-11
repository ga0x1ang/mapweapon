<?php

require_once("curl.php");

$access_token = $_POST["access_token"];
$message = $_POST["message"];


/********

  get the uid of current user

 ********/

function get_current_user_uid()
{
    global $access_token;
    $URL_get_uid = "https://api.weibo.com/2/account/get_uid.json";
    $parameter = 'access_token='.$access_token;

    $user_uid = json_decode(callAPI($URL_get_uid, "GET", $parameter), true);

    $result = $user_uid["uid"];

    return $result;
}

function get_friend_list()
{
    global $access_token;
    $uid = get_current_user_uid();
    $api_url = "https://api.weibo.com/2/friendships/friends/bilateral/ids.json";
    $parameter = "access_token=".$access_token."&uid=".$uid;

    $friendlist = json_decode(callAPI($api_url, "GET", $parameter), true);

    $result = $friendlist["ids"];

    return $result;
}

function get_user_property($uid, $property)
{
    global $access_token;

    $api_url = "https://api.weibo.com/2/users/show.json";
    $parameter = "access_token=".$access_token."&uid=".$uid;

    $properties = json_decode(callAPI($api_url, "GET", $parameter), true);

    $result = $properties[$property];

    return $result;
}

/*****************

property_name:
string, properties from user info
relation:
string, EQUALS (CONTAINS, GREATER, SMALLER, EXCEPT these are not implemented))

returns the filtered user [uid] list

 *****************/

function filter($friendlist, $property_name, $relation, $value)
{
    $result = Array();

    foreach($friendlist as $uid)
    {
        $property = get_user_property($uid, $property_name);

        switch($relation)
        {
            case "EQUALS":
                if(strcmp($property, $value) == 0)
                {
                    array_push($result, $uid);
                };
            case "CONTAINS":
            case "GREATER":
            case "SMALLER":
            case "EXCEPT":
        }
    }

    return $result;
}

/*******************

  query the database and find the value of $tag

  return an Array(property, relation, value)

 *******************/

function processMessage($message)
{
    $friendlist = get_friend_list();

    // step: 1. scan the message and match the tags
    //       2. query the tag database, translate tags to conditions and filtered the friend list
    //       3. replace tags

    $message = "this is a @test message @seems @女人 working now. one more test @";

    // step 1

    preg_match_all("/@\w+|[\x{4e00}-\x{9fa5}]+/u", $message, $tags);

    // SUPRE BIG LOOP
    // FOREACH $TAGS AS $TAG, IF $TAG EXISTS, THEN
    // BEGIN

    $testTag = "@女人";

    foreach(array_shift($tags) as $tag)
    {
        if(strcmp($tag, $testTag) == 0)
        {
            // step 2
            // there need to be a foreach to iterate the tags, 
            //     set the 3 parameters of filter, 
            //     and filter the list
            // following is the code in the foreach block

            $property_name = "gender";
            $relation = "EQUALS";
            $value = 'f';
            $friendlist = filter($friendlist, $property_name, $relation, $value);

            // step 3
            // transfer the uid list into name list

            foreach($friendlist as $uid)
            {
                $namelist .= ' @'.get_user_property($uid, "screen_name");
            }

            $message = str_replace($tag, $namelist, $message); // after the construct of the database, $tags should be replaced by $tag
        }
    }

    $result = $message;

    return $result;
}

function sendMessage()
{
    global $access_token;
    global $message;
    $message = processMessage($message);
    $URL_update = "https://api.weibo.com/2/statuses/update.json";
    $parameter = "access_token=".$access_token."&status=".$message;

    //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded; charset=utf-8"));

    return callAPI($URL_update, $parameter);
}

sendMessage();

?>
