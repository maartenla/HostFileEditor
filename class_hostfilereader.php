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

           //echo($fileString);die;

            file_put_contents($this->windowsHostsFile,$fileString);


        }
        else{
            throw new Exception("File not found");
        }
    }

    //regex: (#+)?(NameVirtualHost)
}
?>