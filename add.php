<?php
require_once("kvdb.php");

//$kv->delete("1679904407u");

$uid = $_POST["uid"];
$tag_name = $_POST["tag_name"];
$property_name = $_POST["property_name"];
$relation = $_POST["relation"];
$value = $_POST["value"];

addTag($uid, $tag_name, $property_name, $relation, $value);
