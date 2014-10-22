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
<style>
	* {
		font-family: Verdana, Tahoma, Arial, sans-serif;
		font-size: 11px;
	}

    h2{
        margin-bottom:0px;
    }

    .host-table{
        padding:0px;
        margin:0px;
    }
    .host-table th{
        text-align: left;
        border-bottom: 1px solid rgba(0, 0, 0, 0.20);
        padding-right: 20px;
    }


    .host-table td{
       padding-bottom:8px;
    }

    .host-table td.status,.delete,.servername{
        padding-left:20px;
    }



</style>
<strong>Quick add</strong><br />
    <sup>Add your project to both files at once</sup>
    <div style='margin-top:5px'>
        <form action="formhandler.php?action=add_all" method="post">
            Add project:<br />
	        <input type='text' value="127.0.0.1" placeholder="IP Address" size="10" name="ipaddress" autocomplete="off">
	        <input type='text' placeholder='domain/servername' name="domain" autocomplete="off">
	        <input type='text' value="c:\xampp\htdocs\" placeholder="document root" name='documentroot' autocomplete="off">
	        <input type='checkbox' name='ssl' value="1" autocomplete="off" id="add_all_ssl"><label for="add_all_ssl">SSL</label>
            <button type='submit'>Add</button>
        </form>

		    <table class="host-table" cellpadding="2" cellspacing="0">
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

<hr>
 <strong>Windows hosts file:</strong><br />
 <sup><?= $oHostFileReader->windowsHostsFile ?></sup>
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
<form action="formhandler.php?action=add_win" method="post">
Add host:<br />
<input type='text' value="127.0.0.1" placeholder="IP Address" size="10" name="ipaddress" autocomplete="off">
<input type='text' placeholder='domain' name="domain" autocomplete="off">
<button type="submit">Add</button>
</form>
</div>
<?
if(!$bError){
    ?>
    <div  style='margin-top:5px'>
        <table class="host-table" cellpadding="2" cellspacing="0">
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

<hr>
<strong>Apache vhosts file:</strong><br />
<sup><?= $oHostFileReader->apacheVHostsFile ?></sup>
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
    <form action="formhandler.php?action=add_apa" method="post">
        Add vhost:<br />
		<input type='text' placeholder="document root" name="documentroot" value="c:\xampp\htdocs\" autocomplete="off">
		<input type='text'  placeholder='servername' name="servername" autocomplete="off"> <button type="submit">Add</button>
    </form>
</div>
<?
if(!$bError){
    ?>
<div  style='margin-top:5px'>
    <table class="host-table" cellpadding="2" cellspacing="0">
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


	<hr>
	<strong>Apache SSL file:</strong><br />
	<sup><?= $oHostFileReader->apacheSSLFile ?></sup>
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
		<form action="formhandler.php?action=add_apassl" method="post">
			Add vhost:<br /><input type='text' placeholder="document root" name="documentroot" autocomplete="off">
			<input type='text' placeholder='servername' name="servername" autocomplete="off">
			<button type="submit">Add</button>
		</form>
	</div>
<?
	if(!$bError){
		?>
		<div  style='margin-top:5px'>
			<table class="host-table" cellpadding="2" cellspacing="0">
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