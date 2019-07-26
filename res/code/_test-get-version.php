<?php
  require_once('./_get-version.php');
  $keymanVersion = new KeymanVersion();
  $ver = $keymanVersion->getVersion('web', 'stable');
  echo "Stable: $ver\n";
  if(empty($ver)) {
    $ver = $keymanVersion->getVersion('web', 'beta');
    echo "Beta: $ver\n";
    if(empty($ver)) {
      $ver = $keymanVersion->getVersion('web', 'alpha');
      echo "Alpha: $ver\n";
      if(empty($ver)) {
        echo "console.log('ERROR: KeymanWeb version not found');";
        exit;
      }
    }
  }
  
  echo $ver;
?>