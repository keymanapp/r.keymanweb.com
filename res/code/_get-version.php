<?php
  require_once('./sitesettings.php');

  class KeymanVersion {
    private $versionJsonFilename, $downloadsApiVersionUrl;

    function __construct() {
       $this->versionJsonFilename = dirname(__FILE__) . '/../../../api.keyman.com/.data/versions.json';
       $this->downloadsApiVersionUrl = 'https://downloads.keyman.com/api/version';
    }

    function remove_utf8_bom($text) {
      $bom = pack('H*','EFBBBF');
      $text = preg_replace("/^$bom/", '', $text);
      return $text;
    }
    
    function getVersion($platform, $level) {
      global $KeymanCloudRootPath;
      
      $json = NULL;  
      
      if($platform != 'web' || $level != 'alpha') {
        // For now, we use alpha from local instance of s.keyman.com because 
        // downloads.keyman.com may be out of sync with s.keyman.com for alpha.
        // As we stablise the CI server, we may be able to eliminate this
        $json = file_get_contents($this->versionJsonFilename);
        if($json === NULL) {
          $json = @file_get_contents("{$this->downloadsApiVersionUrl}/$platform");
        }

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
      
      //
      // For KeymanWeb, we have special version handling for alpha
      //
      
      $d = opendir($KeymanCloudRootPath . "kmw\\engine");
      if($d === FALSE) {
        fatal('Engine Path not found');
      }

      $ver_build = "0.0.0";
      while(($f = readdir($d)) !== FALSE) {
        // If version is a dotted triplet then we use a version compare
        if(preg_match("/^\d+\.\d+\.\d+$/", $f)) {
          if(version_compare($f, $ver_build) > 0) $ver_build = $f;
        }
      }
      
      closedir($d);
      if($ver_build == "0.0.0") {
        return null;
      } else {
        return $ver_build;
      }
    }
  
    function recache() {
      $json = @file_get_contents($this->downloadsApiVersionUrl);
      if($json !== NULL) {
        file_put_contents($this->versionJsonFilename, $json);
      }
    }
  }
?>