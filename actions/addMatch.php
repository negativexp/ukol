<?php
if(isset($_POST["playerOne"]) && isset($_POST["playerTwo"]) && isset($_POST["playerOneScore"]) && isset($_POST["playerTwoScore"]) && isset($_POST["datetime"])) {
    include_once("db.php");
    $db = new Database();
    $db->addMatch($db->htmlspecial($_POST["playerOne"]), $db->htmlspecial($_POST["playerTwo"]), $db->htmlspecial($_POST["playerOneScore"]), $db->htmlspecial($_POST["playerTwoScore"]), $_POST["datetime"]);
}