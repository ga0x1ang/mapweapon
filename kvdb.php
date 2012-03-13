<?php
$kv = new SaeKV();
$kv->init();

function addTag($uid, $tag_name, $property_name, $relation, $value)
{
    global $kv;
    // prepare the val to be stored
    $val = json_encode(array(
                        "tag_name" => $tag_name,
                        "property_name" => $property_name,
                        "relation" => $relation,
                        "value" => $value
                    ));
    // generate a unique key
    do
    {
        $key = mt_rand();
    }
    while($kv->get($key) == false);

    // use the generated key to store the val
    $kv->add($key, $val);

    // determin whether the user exists and add the tagkey value to the uidkey record
    $uidkey = $uid.'u';
    $exist = $kv->get($uidkey);
    if($exist)
    {
        $exist = array_push($exist, $key);
        $kv->set($uidkey, $exist);
    }
    else
    {
        $kv->add($uidkey, $key);
    }
}

function getTagList($uid)
{
    global $kv;
    $tag_list = Array();
    $tag_keys = $kv->get($uid.'u');

    foreach($tag_keys as $tag_key)
    {
        $tag_val_json = $kv->get($tag_key);
        $tag_val = json_decode($tag_val);
        array_push($tag_list, $tag_val);
    }

    $result = $tag_list;

    return $result;
}
