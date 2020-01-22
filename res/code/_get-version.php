<?php
  class KeymanVersion {
    private $downloadsApiVersionUrl;

    function __construct() {
       $this->downloadsApiVersionUrl = 'https://downloads.keyman.com/api/version';
    }

    function remove_utf8_bom($text) {
      $bom = pack('H*','EFBBBF');
      $text = preg_replace("/^$bom/", '', $text);
      return $text;
    }
    
    function getVersion($platform, $level) {
      $json = @file_get_contents("{$this->downloadsApiVersionUrl}/$platform");

      if($json !== NULL && $json !== FALSE) {
        $json = $this->remove_utf8_bom($json);    
        $json = json_decode($json);
        
        if($json !== NULL && $json !== FALSE) {
          if(property_exists($json, $platform)) {
            $json = $json->$platform; 
            if($json !== NULL && property_exists($json, $level)) {
              return $json->$level;
            }
          }
        }
      }
      return null;
    }
  }
?>