<?php
if(isset($_POST["firstName"]) && isset($_POST["lastName"])) {
    include_once("db.php");
    $db = new Database();
    $db->addPlayer($db->htmlspecial($_POST["firstName"]), $db->htmlspecial($_POST["lastName"]));
}