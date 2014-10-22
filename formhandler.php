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

    case "status_apassl": changeStatusApacheSSL();break;

    case"delete_apassl": deleteApacheSSL();break;

    case "add_apassl": addApacheSSL();break;

    case "add_all":addAll();break;

    case "quickadd":quickAdd();break;

	case "restart_apa":restartApache();break;
}

function restartApache(){
	$serviceName = exec('net start | findstr /I "Apache"');
	exec('net stop '.$serviceName.' && net start '.$serviceName);
}

function addAll(){
    try{
        $documentroot = $_POST["documentroot"];
        $domain = $_POST["domain"];
        $ipaddress = $_POST["ipaddress"];

        $oHostFileReader = new HostFileReader();

        $oHostFileReader->addWindowsHost($ipaddress,$domain);

        $oHostFileReader->addApacheVHost($documentroot,$domain);

	    if($_POST['ssl'] == 1){
            $oHostFileReader->addApacheSSL($documentroot,$domain);
	    }

    }
    catch(Exception $e){
        echo($e->getMessage());
        echo("<br><a href='index.php'>click to go back</a>");
        die();
    }

    header("location: index.php");
}


function quickAdd(){
	try{
		$documentroot   = urldecode($_GET["path"]);
		$domain         = urldecode($_GET["domain"]);
		$ipaddress      = '127.0.0.1';

		$oHostFileReader = new HostFileReader();

		$oHostFileReader->addWindowsHost($ipaddress,$domain);

		$oHostFileReader->addApacheVHost($documentroot,$domain);

		if($_GET['ssl'] == 1){
			$oHostFileReader->addApacheSSL($documentroot,$domain);
		}

	}
	catch(Exception $e){
		echo($e->getMessage());
		echo("<br><a href='index.php'>click to go back</a>");
		die();
	}

	header("location: index.php?restartApache=1");
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


function addApacheSSL(){
	try{
		$documentroot = $_POST["documentroot"];
		$servername = $_POST["servername"];

		$oHostFileReader = new HostFileReader();

		$oHostFileReader->addApacheSSL($documentroot,$servername);
	}
	catch(Exception $e){
		echo($e->getMessage());
		echo("<br><a href='index.php'>click to go back</a>");
		die();
	}

	header("location: index.php");
}

function deleteApacheSSL(){
	try{
		$servername = $_GET["servername"];

		$oHostFileReader = new HostFileReader();

		$oHostFileReader->deleteApacheSSL($servername);
	}
	catch(Exception $e){
		echo($e->getMessage());
		echo("<br><a href='index.php'>click to go back</a>");
		die();
	}

	header("location: index.php");
}

function changeStatusApacheSSL(){
	$servername = $_GET["servername"];
	$status = $_GET["to"];

	$oHostFileReader = new HostFileReader();

	$oHostFileReader->changeApacheSSLStatus($servername,$status);

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