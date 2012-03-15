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
    while($kv->get($key) != false);

    // use the generated key to store the val
    $kv->add($key, $val);

    // determin whether the user exists and add the tagkey value to the uidkey record
    $uidkey = $uid.'u';
    $exist = json_decode($kv->get($uidkey), true);
    if($exist != NULL)
    {
        array_push($exist, $key);
        $keys = json_encode($exist);
        $kv->set($uidkey, $keys);
    }
    else
    {
        $keys_arr = array();
        array_push($keys_arr, $key);
        $keys = json_encode($keys_arr);
        $kv->add($uidkey, $keys);
    }
}

function getTagList($uid)
{
    global $kv;
    $tag_list = array();
    $uidkey = $uid.'u';
    $tag_keys = json_decode($kv->get($uidkey), true);

    foreach($tag_keys as $tag_key)
    {
        $tag_val_json = $kv->get($tag_key);
        $tag_val = json_decode($tag_val_json, true);
        array_push($tag_list, $tag_val);
    }
    $result = $tag_list;
    return $result;
}
