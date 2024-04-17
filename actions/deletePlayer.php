<?php
if(isset($_GET["id"])) {
    include_once("db.php");
    $db = new Database();
    $db->deletePlayer($db->htmlspecial($_GET["id"]));
}