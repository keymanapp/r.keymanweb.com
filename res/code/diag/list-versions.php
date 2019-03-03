<?php
  require_once('../sitesettings.php');

  $d = opendir($KeymanCloudRootPath . "kmw\\engine");
  if($d === FALSE) {
    fatal('Engine Path not found');
  }
  
  header('Content-Type: text/plain');
  
  echo "Diagnostic: list latest versions of KeymanWeb Engine available on s.keyman.com\n";
  
  $ver_build = "0.0.0";
  while(($f = readdir($d)) !== FALSE) {
    // If version is a dotted triplet then we use a version compare
    //echo $f . "\n";
    if(preg_match("/^\d+\.\d+\.\d+$/", $f)) {
      echo "$f\n";
      if(version_compare($f, $ver_build[0]) > 0) $ver_build = $f;
    }
  }
  
  closedir($d);
  
  echo "Latest version: $ver_build\n";
?>
