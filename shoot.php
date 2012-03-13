<?php
require_once("kvdb.php");
require_once("curl.php");

$access_token = $_POST["access_token"];
$uid = $_POST["uid"];

/*********

 get the friend list (bilateral) uid list

 return an array

*********/
function get_friend_list()
{
    global $access_token;
    global $uid;
    $api_url = "https://api.weibo.com/2/friendships/friends/bilateral/ids.json";
    $parameter = "access_token=".$access_token."&uid=".$uid;

    $friendlist = json_decode(callAPI($api_url, "GET", $parameter), true);

    $result = $friendlist["ids"];

    return $result;
}

/************

get a specified property value from a specified user

type of the return value depends on the property value type

************/
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

returns the filtered user [uid] list(array)

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
    //global $message;
    $friendlist = get_friend_list();
    $user_uid = global $uid;
    $defined_tags = getTagList($user_uid);

    // step: 1. scan the message and match the tags
    //       2. query the tag database, translate tags to conditions and filtered the friend list
    //       3. replace tags

    // step 1

    // get the tag from the raw message
    preg_match_all("/@\w+|[\x{4e00}-\x{9fa5}]+/u", $message, $tags);

    foreach($defined_tags as $defined_tag)
    {
        foreach($tags as $tag)
        {
            if(strcmp($tag, $defined_tag["tag_name"]) == 0)
            {
                // step 2
                // there need to be a foreach to iterate the tags, 
                //     set the 3 parameters of filter, 
                //     and filter the list
                // following is the code in the foreach block

                $property_name = $defined_tag["property_name"];
                $relation = $defined_tag["relation"];
                $value = $defined_tag["value"];

                $friendlist = filter($friendlist, $property_name, $relation, $value);

                // step 3
                // transfer the uid list into name list

                foreach($friendlist as $uid)
                {
                    $namelist .= ' @'.get_user_property($uid, "screen_name");
                }

                $message = str_replace($tag, $namelist, $message);
            }
        }
        $result = $message;
        return $result;
    }
}
/****************************

send the processed message

return 0 or other ...

****************************/
function sendMessage()
{
    global $access_token;
    $message = $_POST["message"];
    $message = processMessage($message);
    $url = "https://api.weibo.com/2/statuses/update.json";
    $parameter = "access_token=".$access_token."&status=".$message;

    $result = callAPI($url, "POST", $parameter);

    return $result;
}

sendMessage();

?>
