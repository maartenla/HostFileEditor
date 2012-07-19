<?php
include_once("class_hostfilereader.php");

$oHostFileReader = new HostFileReader();
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
        <form action="formhandler.php?action=add_both" method="post">
            Add project:<br /><input type='text' placeholder="IP Adress" size="10" name="ipaddress" autocomplete="off"> <input type='text' placeholder='domain/servername' name="domain" autocomplete="off"> <input type='text' placeholder="document root" name='documentroot' autocomplet="off">
            <button type='submit'>Add</button>
        </form>

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
Add host:<br /><input type='text' placeholder="IP Adress" name="ipaddress" size="10" autocomplete="off"> <input type='text' placeholder='domain' name="domain" autocomplete="off"> <button type="submit">Add</button>
</form>
</div>
<?
if(!$bError){
    ?>
    <div  style='margin-top:5px'>
        <table class="host-table" cellpadding="2" cellspacing="0">
            <thead>
                <tr>
                    <th>IP Adress</th><th>Domain</th><th>&nbsp;</th><th>&nbsp;</th>
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
                        <td><a href="<?= $aWindowsHost[3] ?>" target="_blank"><?= $aWindowsHost[3] ?></a></td>
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
        Add vhost:<br /><input type='text' placeholder="document root" name="documentroot" autocomplete="off"> <input type='text' placeholder='servername' name="servername" autocomplete="off"> <button type="submit">Add</button>
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
            foreach($aApacheHosts as $aApacheHost){
                if(stripos($aApacheHost[0],"#") === 0){
                    $sStatusImage = "images/bullet_red.png";
                    $toStatus = "e";
                }
                else{
                    $sStatusImage = "images/bullet_green.png";
                    $toStatus = "d";
                }
                ?>
            <tr>
                <td><?= $aApacheHost[1] ?></td>
                <td class="servername"><a href="<?= $aApacheHost[2] ?>" target="_blank"><?= $aApacheHost[2] ?></a></td>
                <td class="status"><a href="formhandler.php?action=status_apa&to=<?= $toStatus ?>&servername=<?= $aApacheHost[2] ?>"><img src="<?=$sStatusImage?>" alt="enable/disable" /></a></td>
                <td class="delete"><a href="formhandler.php?action=delete_apa&servername=<?= $aApacheHost[2] ?>"><img src="images/delete_16.png" alt="delete" /></a></td>
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