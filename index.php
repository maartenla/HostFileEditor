<?php
include_once("class_hostfilereader.php");

$oHostFileReader = new HostFileReader();
?>
<style>
    .host-table{
        padding:0px;
        margin:0px;
    }
    .host-table th{
        text-align: left;
        border-bottom: 1px solid rgba(0, 0, 0, 0.20);
        padding-right: 60px;
    }


    .host-table td{
       padding-bottom:8px;


    }

    .host-table td.status,.delete{
        padding-left:30px;
    }



</style>
<h1>XAMPP Host File Editor</h1>
Easy way to edit your hostfiles

<h2>File locations</h2>
<div>
    <form>
        <div style='margin-bottom:20px'>
            <div style="width:450px">Windows HOSTS file location:</div>
            <div><input type='text' value='<?= $oHostFileReader->windowsHostsFile ?>' style='width:450px'></div>
        </div>
        <div style='margin-bottom: 20px'>
            <div>XAMPP Apache httpd-vhosts.conf location:</div>
            <div><input type='text' value='<?= $oHostFileReader->apacheVHostsFile ?>' style='width:450px'></div>
        </div>
        <div>
            <button type='submit'>Save locations</button>
        </div>
    </form>
</div>
<hr>
 <h2 style='margin-bottom: 0px'>Windows hosts file:</h2>
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

<div style='margin-top:25px'>
<form action="formhandler.php?action=add_win" method="post">
Add host: <input type='text' placeholder="IP Adress" name="ipaddress" autocomplete="off"> <input type='text' placeholder='domain' name="domain" autocomplete="off"> <button type="submit">Add</button>
</form>
</div>
<?
if(!$bError){
    ?>
    <div  style='margin-top:25px'>
        <table class="host-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>IP Adress</th><th>Domain</th><th>&nbsp;</th><th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?
                foreach($aWindowsHosts as $aWindowsHost){
                    if($aWindowsHost[1] == "#"){
                        $sStatus = "Enable";
                        $toStatus = "e";
                    }
                    else{
                        $sStatus = "Disable";
                        $toStatus = "d";
                    }
                    ?>
                    <tr>
                        <td><?= $aWindowsHost[2] ?></td>
                        <td><a href="<?= $aWindowsHost[3] ?>" target="_blank"><?= $aWindowsHost[3] ?></a></td>
                        <td class="status"><a href="formhandler.php?action=status_win&to=<?= $toStatus ?>&domain=<?= $aWindowsHost[3] ?>&ip=<?= $aWindowsHost[2] ?>"><?= $sStatus ?></a></td>
                        <td class="delete"><a href="formhandler.php?action=delete_win&domain=<?= $aWindowsHost[3] ?>&ipaddress=<?= $aWindowsHost[2] ?>">delete</a></td>
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