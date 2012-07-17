<?php
include_once("class_hostfilereader.php");


switch($_GET["action"]){
    case "status_win": changeStatusWin();        break;

    case "delete_win": deleteWin();              break;

    case "add_win":     addWin();               break;
}

function changeStatusWin(){
    $domain = $_GET["domain"];
    $status = $_GET["to"];
    $ipaddress = $_GET["ip"];

    $oHostFileReader = new HostFileReader();

    $oHostFileReader->changeWindowsHostLineStatus($domain,$status,$ipaddress);

    header("Location: index.php");
}

function deleteWin(){
    $domain = $_GET["domain"];
    $ipaddress = $_GET["ipaddress"];

    $oHostFileReader = new HostFileReader();

    $oHostFileReader->deleteWindowsHost($ipaddress,$domain);

    header("location: index.php");
}

function addWin(){
    $domain = $_POST["domain"];
    $ipaddress = $_POST["ipaddress"];

    $oHostFileReader = new HostFileReader();
    try{
        $oHostFileReader->addWindowsHost($ipaddress,$domain);
    }
    catch(Exception $e){
        echo($e->getMessage());
        echo("<br><a href='index.php'>click to go back</a>");
    }

    header("Location: index.php");
}

?>