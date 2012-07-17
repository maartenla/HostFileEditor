<?php

class HostFileReader{

    public $windowsHostsFile, $apacheVHostsFile;

    public function __construct(){
        //change if file is in different location
        $this->windowsHostsFile = "C:\\WINDOWS\\system32\\drivers\\etc\\hosts";
        //change if file is in different location
        $this->apacheVHostsFile = "C:\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf";
    }

    public function getWindowsHosts(){

        if(file_exists($this->windowsHostsFile)){
            $result = array();
            $file = file($this->windowsHostsFile);

            foreach($file as $line){
                if($this->isValidWindowsHostFileLine($line)){
                    $IPDomain = $this->extractIpAndDomainFromWindowsHostFileLine($line);
                    foreach($IPDomain as $key => $val){
                        $IPDomain[$key] = trim($val);
                    }
                    $result[] = $IPDomain;
                }
            }

            return $result;
        }
        else{
            throw new Exception("File not found");
        }
    }

    private function isValidWindowsHostFileLine($line){

        if( preg_match("/(#?)\s*\b((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))\b\s+([^#]+)(?:#.+)?/i",$line) > 0){
            return true;
        }
         return false;
    }

    private function extractIpAndDomainFromWindowsHostFileLine($line){
        $matches = array();

        preg_match("/(#?)\s*\b((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))\b\s+([^#]+)(?:#.+)?/i",$line,$matches);

        return $matches;
    }

    public function changeWindowsHostLineStatus($domain,$toStatus,$ipaddress){
        if(file_exists($this->windowsHostsFile)){
            $result = array();
            $file = file($this->windowsHostsFile);

            $fileString = "";

            foreach($file as $key => $line){
                if($this->isValidWindowsHostFileLine($line)){
                    $IPDomain = $this->extractIpAndDomainFromWindowsHostFileLine($line);

                   if(trim($IPDomain[3]) == $domain && trim($IPDomain[2]) == $ipaddress){
                       if($toStatus == "d" && $IPDomain[1] != "#"){
                           $file[$key] = "#".$file[$key];
                       }
                       elseif($toStatus == "e" && $IPDomain[1] == "#"){
                            $file[$key] = ltrim($file[$key],"#");
                       }
                   }
                }
                $fileString .= $file[$key];
            }

           file_put_contents($this->windowsHostsFile,$fileString);


        }
        else{
            throw new Exception("File not found");
        }
    }

    public function addWindowsHost($ipadress,$domain){
        if(file_exists($this->windowsHostsFile)){
            if(preg_match("/\b((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))\b/i",$ipadress) < 1){
                throw new Exception("Invalid ip address");
            }

            $domain = trim($domain);
            if($domain == ""){
                throw new Exception("Domain must not be empty");
            }

            file_put_contents($this->windowsHostsFile,$ipadress." ".$domain.PHP_EOL,FILE_APPEND);
        }
        else{
            throw new Exception("File not found");
        }
    }

    public function deleteWindowsHost($ipaddress, $domain){

        if(file_exists($this->windowsHostsFile)){

            $file = file($this->windowsHostsFile);

            $fileString = "";

            foreach($file as $key => $line){

                if($this->isValidWindowsHostFileLine($line)){

                    $IPDomain = $this->extractIpAndDomainFromWindowsHostFileLine($line);

                    if(trim($IPDomain[3]) == $domain && trim($IPDomain[2]) == $ipaddress){
                        unset($file[$key]);
                    }
                }
                if($file[$key] != ""){
                 $fileString .= $file[$key];
                }
            }

            file_put_contents($this->windowsHostsFile,$fileString);
        }
        else{
            throw new Exception("File not found");
        }
    }

    public function getApacheHosts(){
        if(file_exists($this->apacheVHostsFile)){
            $file = file_get_contents($this->apacheVHostsFile);

            $matches = array();

            $regex = '/(#*)\s*<VirtualHost \*.+?((?:DocumentRoot ".+?")|(?:ServerName .+?\r)).*?((?:DocumentRoot ".+?")|(?:ServerName .+?\r)).*?<\/VirtualHost>/ism';

            preg_match_all($regex,$file,$matches);

            $servers = array();

           foreach($matches[1] as $key => $val){
               if(stripos($matches[2][$key],"documentroot") === 0){
                   $documentroot = trim(str_ireplace("documentroot","",$matches[2][$key]));
                   $documentroot = str_replace("\"","",$documentroot);
                   $servername = trim(str_ireplace("servername","",$matches[3][$key]));

                   $servers[] = array($val,$documentroot,$servername);
               }
               elseif(stripos($matches[2][$key],"servername") === 0){
                   $documentroot = trim(str_ireplace("documentroot","",$matches[3][$key]));
                   $documentroot = str_replace("\"","",$documentroot);
                   $servername = trim(str_ireplace("servername","",$matches[2][$key]));

                   $servers[] = array($val,$documentroot,$servername);
               }
           }

           return $servers;
        }
        else{
            throw new Exception("File not found");
        }
    }

    public function changeApacheVHostStatus($servername,$tostatus){
        if(file_exists($this->apacheVHostsFile)){
            if(trim($servername) == ""){
                throw new Exception("Servername empty");
            }
            $file = file($this->apacheVHostsFile);

            $iVHostStart = -1;

            $aTmp = array();

            $fileString = "";

            $bProcessTmp = false;
            foreach($file as $key => $line){
                if(preg_match("/(?:#*)\s*(<virtualhost)/i",$line) > 0){
                    $iVHostStart = $key;
                }
                if(preg_match("/servername\s*(".$servername.")/i",$line) > 0){
                    $bProcessTmp = true;
                }
                if($iVHostStart > 0){
                    $aTmp[$key]=$line;
                }
                if(preg_match("/(?:#*)\s*(<\/virtualhost)/i",$line) > 0){

                    if($bProcessTmp){

                        foreach($aTmp as $tmpkey=>$tmpline){
                            if(preg_match("/(#)/",$tmpline) > 0 && $tostatus == "e"){

                                $file[$tmpkey] = str_replace("#","",$tmpline);
                            }
                            else{
                                if($tostatus == "d" && preg_match("/(#)/",$tmpline) < 1){
                                    $file[$tmpkey] = "##".$tmpline;
                                }
                            }
                        }
                        $bProcessTmp = false;
                    }

                    $aTmp = array();
                    $iVHostStart = -1;
                }
            }

            foreach($file as $line){
                $fileString.=$line;
            }
            file_put_contents($this->apacheVHostsFile,$fileString);
        }
        else{
            throw new Exception("File not found");
        }
    }

    public function deleteApacheVHost($servername){
        if(file_exists($this->apacheVHostsFile)){

            if(trim($servername) == ""){
                throw new Exception("Servername empty");
            }


            $file = file($this->apacheVHostsFile);

            $iVHostStart = -1;

            $aTmp = array();

            $fileString = "";

            $bProcessTmp = false;
            foreach($file as $key => $line){
                if(preg_match("/(?:#*)\s*(<virtualhost)/i",$line) > 0){
                    $iVHostStart = $key;
                }
                if(preg_match("/servername\s*(".$servername.")/i",$line) > 0){
                    $bProcessTmp = true;
                }
                if($iVHostStart > 0){
                    $aTmp[$key]=$line;
                }
                if(preg_match("/(?:#*)\s*(<\/virtualhost)/i",$line) > 0){

                    if($bProcessTmp){

                        foreach($aTmp as $tmpkey=>$tmpline){
                            unset($file[$tmpkey]);
                        }
                        $bProcessTmp = false;
                    }
                    $aTmp = array();
                    $iVHostStart = -1;
                }
            }

            foreach($file as $line){
                $fileString.=$line;
            }
            file_put_contents($this->apacheVHostsFile,$fileString);
        }
        else{
            throw new Exception("File not found");
        }
    }

    public function addApacheVHost($documentroot,$servername){
        $documentroot = trim($documentroot);
        $servername = trim($servername);
        if($documentroot == ""){
            throw new Exception("Document root cannot be empty");
        }

        if($servername == ""){
            throw new Exception("Servername cannot be empty");
        }

        $content = PHP_EOL."<VirtualHost *>".PHP_EOL;
        $content .= "DocumentRoot \"".$documentroot."\"".PHP_EOL;
        $content .= "ServerName ".$servername.PHP_EOL;
        $content .= "<Directory \"".$documentroot."\">".PHP_EOL;
        $content .= "Order allow,deny".PHP_EOL;
        $content .= "Allow from all".PHP_EOL;
        $content .= "</Directory>".PHP_EOL;
        $content .= "</VirtualHost>";

        if(file_exists($this->apacheVHostsFile)){
            file_put_contents($this->apacheVHostsFile,$content,FILE_APPEND);
        }
        else{
            throw new Exception("File not found");
        }
    }
    //regex: (#+)?(NameVirtualHost)
}
?>