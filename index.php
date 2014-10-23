<?php
include_once("class_hostfilereader.php");
$projectPath = 'C:'.DIRECTORY_SEPARATOR .'xampp'.DIRECTORY_SEPARATOR .'htdocs'.DIRECTORY_SEPARATOR;

$oHostFileReader = new HostFileReader();

try{
	$aWindowsHosts = $oHostFileReader->getWindowsHosts();
}
catch(Exception $e){
	$bError = true;
	$sMsg = $e->getMessage();
}

function hasHost($host){
	global $aWindowsHosts;
	foreach($aWindowsHosts as $whost){
		if(strpos(strtolower($whost[3]), strtolower($host)) !== false){
			return true;
		}
	}
	return false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script>
	var apacheRestart = function() {
	$("#messagebar").css("-webkit-transition-duration", "0s");
	$("#messagebar").width("0%");
	window.setTimeout(function() { $("#messagebar").css("-webkit-transition-duration", "5s"); }, 400);
	
	$("#messageBarShow").slideDown(250);
	$("#messagebar").addClass("active");
	$("#apacheButton").attr("disabled", "disabled");
	var didItFail = 0;
	$("#messagebar").width("20%");
	$("#messagebar").html("Restarting Apache...");
		
	$.ajax({
			type: 'GET',
			url: 'formhandler.php?action=restart_apa',
			timeout: 400,
			success: function(data, textStatus, XMLHttpRequest) { },
			error: function(XMLHttpRequest, textStatus, errorThrown) {	}
		});
	var intervalScript = window.setInterval(function() {
		$.ajax({
			type: 'GET',
			url: window.location.pathname,
			timeout: 1000,
			success: function(data, textStatus, XMLHttpRequest) { 
				if(didItFail == 1){
					//window.location = window.location.pathname;
					$("#messagebar").html("Apache is running.");
					$("#messagebar").attr("data-dismiss","alert");
					$("#messagebar").removeClass("active");
					$("#apacheButton").removeAttr("disabled");
					window.setTimeout(function() { $("#messageBarShow").slideUp(1000); }, 2000);
					clearInterval(intervalScript);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if(didItFail == 0){
					$("#messagebar").width("40%");
					$("#messagebar").html("Apache stopped, restarting...");
					window.setTimeout(function() { 
						$("#messagebar").css("-webkit-transition-duration", "20s");
						$("#messagebar").width("100%");	 
					}, 5000);
					
				}
				didItFail = 1;
			}
		});
	}, 2000);
};
	</script>
<?
if(isset($_GET['restartApache']) && $_GET['restartApache'] == 1){
?>
<script>
$( document ).ready( apacheRestart(); );
</script>
<?
}
?>
<title>Hostfile Editor</title>
</head>
<body>
	<div class="container">
		<div class="progress" id="messageBarShow" style="display:none;">
  <div id="messagebar" class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
  </div>
</div>
		<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">
        HostFileEditor
      </a>
    </div>
	  <form class="navbar-form navbar-right">
	  	<button id="apacheButton" onClick="apacheRestart()" type="button" class="btn btn-primary"><i class="fa fa-refresh"></i> Restart Apache</button>
	  </form>
  </div>
</nav>		
<h4>Quick add</h4>
    <div>Add your project to both files at once</div>
    <div style='margin-top:5px'>
        <form class="form-inline" action="formhandler.php?action=add_all" method="post">
            Add project:<br />
	        <input class="form-control" type='text' value="127.0.0.1" placeholder="IP Address" size="10" name="ipaddress" autocomplete="off">
	        <input class="form-control" type='text' placeholder='domain/servername' name="domain" autocomplete="off">
	        <input class="form-control" type='text' value="c:\xampp\htdocs\" placeholder="document root" name='documentroot' autocomplete="off">
	        <input type='checkbox' name='ssl' value="1" autocomplete="off" id="add_all_ssl"><label for="add_all_ssl">SSL</label>
            <button  class="btn btn-default" type='submit'>Add</button>
        </form>

		    <table class="table table-striped table-condensed" cellpadding="2" cellspacing="0">
			    <thead>
			    <tr>
				    <th>IP Address</th>
				    <th>Directory</th>
				    <th>&nbsp;</th>
				    <th>Domain</th>
				    <th>&nbsp;</th>
				    <th>&nbsp;</th>
				    <th>&nbsp;</th>
				    <th>&nbsp;</th>
			    </tr>
			    </thead>
			    <tbody>
			    <?
			    foreach(scandir($projectPath) as $dir) {
				    $path = $projectPath.$dir;
                    
                    $possibledocumentroots = ['www', 'STAR', 'private_html'];
                    
                    if(!file_exists($path . DIRECTORY_SEPARATOR . 'index.php') || !file_exists($path . DIRECTORY_SEPARATOR . 'index.html')){
                        foreach($possibledocumentroots as $documentroot){
                            if(file_exists($path . DIRECTORY_SEPARATOR.$documentroot.DIRECTORY_SEPARATOR.'index.php')){
                                $path = $path . DIRECTORY_SEPARATOR.$documentroot;
                            }
                        }
                    }
                    
				    if(hasHost($dir)){
					    continue;
				    }

				    //exceptions
				    if(!in_array($dir, ['.', '..']) && strpos(strtolower($dir), 'hostfile') === false && is_dir($path)) {

					    ?>
					    <tr>
						    <td>127.0.0.1</td>
						    <td><?= $path ?></td>
						    <td></td>
						    <td><?= strtolower($dir) ?>.local</td>
						    <td></td>
						    <td><a href="formhandler.php?action=quickadd&path=<?=urlencode($path)?>&domain=<?=urlencode(strtolower($dir).'.local')?>">Add as HTTP</a></td>
						    <td></td>
						    <td><a href="formhandler.php?action=quickadd&path=<?=urlencode($path)?>&domain=<?=urlencode(strtolower($dir).'.local')?>&ssl=1">Add as HTTPS</a></td>
					    </tr>
				    <?
				    }
			    }

			    ?>

			    </tbody>
		    </table>
    </div>

 <strong>Windows hosts file:</strong><br />
 <div><?= $oHostFileReader->windowsHostsFile ?></div>
<?
$bError = false;
$sMsg = "";
try{
    $aWindowsHosts = $oHostFileReader->getWindowsHosts();
}
catch(Exception $e){
    $bError = true;
    $sMsg = $e->getMessage();
}
?>

<div style='margin-top:5px'>
<form class="form-inline" action="formhandler.php?action=add_win" method="post">
Add host:<br />
<input class="form-control" type='text' value="127.0.0.1" placeholder="IP Address" size="10" name="ipaddress" autocomplete="off">
<input class="form-control" type='text' placeholder='domain' name="domain" autocomplete="off">
<button class="btn btn-default" type="submit">Add</button>
</form>
</div>
<?
if(!$bError){
    ?>
    <div  style='margin-top:5px'>
        <table class="table table-striped" cellpadding="2" cellspacing="0">
            <thead>
                <tr>
                    <th>IP Address</th><th>Domain</th><th>&nbsp;</th><th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?
                foreach($aWindowsHosts as $aWindowsHost){
                    if($aWindowsHost[1] == "#"){
                        $sStatusImage = "images/bullet_red.png";
                        $toStatus = "e";
                    }
                    else{
                        $sStatusImage = "images/bullet_green.png";
                        $toStatus = "d";
                    }
                    ?>
                    <tr>
                        <td><?= $aWindowsHost[2] ?></td>
                        <td><a href="http://<?= $aWindowsHost[3] ?>" target="_blank"><?= $aWindowsHost[3] ?></a></td>
                        <td class="status"><a href="formhandler.php?action=status_win&to=<?= $toStatus ?>&domain=<?= $aWindowsHost[3] ?>&ip=<?= $aWindowsHost[2] ?>"><img src="<?=$sStatusImage?>" alt="enable/disable" /></a></td>
                        <td class="delete"><a href="formhandler.php?action=delete_win&domain=<?= $aWindowsHost[3] ?>&ipaddress=<?= $aWindowsHost[2] ?>"><img src="images/delete_16.png" alt="delete" /></a></td>
                    </tr>
                    <?
                }

                ?>

            </tbody>



        </table>
    </div>
    <?
}
else{
    echo($sMsg);
}
?>

<strong>Apache vhosts file:</strong><br />
<div><?= $oHostFileReader->apacheVHostsFile ?></div>
<?
$bError = false;
$sMsg = "";
try{
   $aApacheHosts =  $oHostFileReader->getApacheHosts();
}
catch(Exception $e){
    $bError = true;
    $sMsg = $e->getMessage();
}
?>
<div style='margin-top:5px'>
    <form class="form-inline" action="formhandler.php?action=add_apa" method="post">
        Add vhost:<br />
		<input class="form-control" type='text' placeholder="document root" name="documentroot" value="c:\xampp\htdocs\" autocomplete="off">
		<input class="form-control" type='text'  placeholder='servername' name="servername" autocomplete="off"> <button class="btn btn-default"  type="submit">Add</button>
    </form>
</div>
<?
if(!$bError){
    ?>
<div  style='margin-top:5px'>
    <table class="table table-striped" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
            <th>Documentroot</th><th class="servername">Servername</th><th>&nbsp;</th><th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            <?
            foreach($aApacheHosts as $aApacheSSLHost){
                if(stripos($aApacheSSLHost[0],"#") === 0){
                    $sStatusImage = "images/bullet_red.png";
                    $toStatus = "e";
                }
                else{
                    $sStatusImage = "images/bullet_green.png";
                    $toStatus = "d";
                }
                ?>
            <tr>
                <td><?= $aApacheSSLHost[1] ?></td>
                <td class="servername"><a href="http://<?= $aApacheSSLHost[2] ?>" target="_blank"><?= $aApacheSSLHost[2] ?></a></td>
                <td class="status"><a href="formhandler.php?action=status_apa&to=<?= $toStatus ?>&servername=<?= $aApacheSSLHost[2] ?>"><img src="<?=$sStatusImage?>" alt="enable/disable" /></a></td>
                <td class="delete"><a href="formhandler.php?action=delete_apa&servername=<?= $aApacheSSLHost[2] ?>"><img src="images/delete_16.png" alt="delete" /></a></td>
            </tr>
                <?
            }

            ?>

        </tbody>



    </table>
</div>
<?
}
else{
    echo($sMsg);
}
?>


	<strong>Apache SSL file:</strong><br />
	<div><?= $oHostFileReader->apacheSSLFile ?></div>
<?
	$bError = false;
	$sMsg = "";
	try{
		$aApacheSSLHosts =  $oHostFileReader->getApacheSSLHosts();
	}
	catch(Exception $e){
		$bError = true;
		$sMsg = $e->getMessage();
	}
?>
	<div style='margin-top:5px'>
		<form class="form-inline" action="formhandler.php?action=add_apassl" method="post">
			Add vhost:<br /><input class="form-control" type='text' placeholder="document root" name="documentroot" autocomplete="off">
			<input class="form-control" type='text' placeholder='servername' name="servername" autocomplete="off">
			<button class="btn btn-default" type="submit">Add</button>
		</form>
	</div>
<?
	if(!$bError){
		?>
		<div  style='margin-top:5px'>
			<table class="table table-striped" cellpadding="2" cellspacing="0">
				<thead>
					<tr>
						<th>Documentroot</th><th class="servername">Servername</th><th>&nbsp;</th><th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?
						foreach($aApacheSSLHosts as $aApacheSSLHost){
							if(stripos($aApacheSSLHost[0],"#") === 0){
								$sStatusImage = "images/bullet_red.png";
								$toStatus = "e";
							}
							else{
								$sStatusImage = "images/bullet_green.png";
								$toStatus = "d";
							}
							?>
							<tr>
								<td><?= $aApacheSSLHost[1] ?></td>
								<td class="servername"><a href="https://<?= $aApacheSSLHost[2] ?>" target="_blank"><?= $aApacheSSLHost[2] ?></a></td>
								<td class="status"><a href="formhandler.php?action=status_apassl&to=<?= $toStatus ?>&servername=<?= $aApacheSSLHost[2] ?>"><img src="<?=$sStatusImage?>" alt="enable/disable" /></a></td>
								<td class="delete"><a href="formhandler.php?action=delete_apassl&servername=<?= $aApacheSSLHost[2] ?>"><img src="images/delete_16.png" alt="delete" /></a></td>
							</tr>
						<?
						}

					?>

				</tbody>



			</table>
		</div>
	<?
	}
	else{
		echo($sMsg);
	}
?>
		</div>
	</body>
</html>