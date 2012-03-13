<?php
require("kvdb.php");

$uid = $_POST["uid"];

$defined_tags = getTagList($uid+'u');

if(isset($_POST["tag_name"]))
{
    addTag($uid+'u', $_POST["tag_name"], $_POST["property_name"], $_POST["relation"], $_POST["value"]);
}

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>某科学的超地图炮 - 控制台</title>
    </head>
    <body>
        <table border="1">
            <tr>
                <th>tag_name</th>
                <th>property_name</th>
                <th>relation</th>
                <th>value</th>
            </tr>
            <?php
                foreach($defined_tags = $defined_tag)
                {
                    echo "<tr>";
                        echo "<td>".$defined_tag["tag_name"]."</td>";
                        echo "<td>".$defined_tag["property_name"]."</td>";
                        echo "<td>".$defined_tag["relation"]."</td>";
                        echo "<td>".$defined_tag["value"]."</td>";
                    echo "</tr>"
                }
            ?>
        </table>
        <br />
        <form name="add_form" method="POST" action="">
            tag_name: <input type="text" name="tag_name">
            <br>
            property_name: <input type="text" name="property_name">
            <br>
            relation: <input type="text" name="relation">
            <br>
            value: <input type="text" name="value">
        </form>
    </body>
</html>
