<?php
include_once("class_hostfilereader.php");

foreach($_GET as $key=>$val){
    $_GET[$key] = trim($val);
}

foreach($_POST as $key=>$val){
    $_POST[$key] = trim($val);
}

switch($_GET["action"]){
    case "status_win": changeStatusWin();        break;

    case "delete_win": deleteWin();              break;

    case "add_win":     addWin();               break;

    case "status_apa": changeStatusApache();break;

    case"delete_apa": deleteApache();break;

    case "add_apa": addApache();break;

    case "add_both":addBoth();break;

}

function addBoth(){
    try{
        $documentroot = $_POST["documentroot"];
        $domain = $_POST["domain"];
        $ipaddress = $_POST["ipaddress"];

        $oHostFileReader = new HostFileReader();

        $oHostFileReader->addWindowsHost($ipaddress,$domain);

        $oHostFileReader->addApacheVHost($documentroot,$domain);

    }
    catch(Exception $e){
        echo($e->getMessage());
        echo("<br><a href='index.php'>click to go back</a>");
        die();
    }

    header("location: index.php");
}

function addApache(){
    try{
        $documentroot = $_POST["documentroot"];
        $servername = $_POST["servername"];

        $oHostFileReader = new HostFileReader();

        $oHostFileReader->addApacheVHost($documentroot,$servername);
    }
    catch(Exception $e){
        echo($e->getMessage());
        echo("<br><a href='index.php'>click to go back</a>");
        die();
    }

    header("location: index.php");
}

function deleteApache(){
    try{
        $servername = $_GET["servername"];

        $oHostFileReader = new HostFileReader();

        $oHostFileReader->deleteApacheVHost($servername);
    }
    catch(Exception $e){
        echo($e->getMessage());
        echo("<br><a href='index.php'>click to go back</a>");
        die();
    }

    header("location: index.php");


}

function changeStatusApache(){
    $servername = $_GET["servername"];
    $status = $_GET["to"];

    $oHostFileReader = new HostFileReader();

    $oHostFileReader->changeApacheVHostStatus($servername,$status);

    header("location: index.php");

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