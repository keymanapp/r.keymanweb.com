<?php
  require_once('_get-version.php');
  
  function fail($message) {
    header("HTTP/1.0 400 $message");
    exit;
  }
  
  function fatal($message) {
    header("HTTP/1.0 500 $message");
    exit;
  }

  $keymanVersion = new KeymanVersion();
  
  /*
    Test for stability parameter. If not provided, assume 'stable'
  */
  
  if(!empty($_REQUEST['level'])) {
    $level = $_REQUEST['level'];
    if(!preg_match('/^(stable|beta|alpha)$/', $level)) {
      fail('Invalid level parameter - stable, beta, or alpha expected');
    }
  } else {
    $level = 'stable';
  }
  
  if(!empty($_REQUEST['platform'])) {
    $platform_web_legacy = false;
    $platform = $_REQUEST['platform'];
    if(!preg_match('/^(android|ios|mac|windows|web)$/', $platform)) {
      fail('Invalid platform parameter - android, ios, mac, windows or web expected');
    }
  } else {
    $platform_web_legacy = true;
    $platform = 'web';
  }
  
  if(isset($_REQUEST['command']) && $_REQUEST['command'] == 'cache') {
    // We'll refresh the backend cache, without testing the JSON data first
    $keymanVersion->recache();
  }
  
  $ver = $keymanVersion->getVersion($platform, $level);
  
  if($platform_web_legacy) {
    if(empty($ver) && $level == 'stable') {
      $level = 'beta';
      $ver = $keymanVersion->getVersion($platform, $level);
    }
    if(empty($ver) && $level == 'beta') {
      $level = 'alpha';
      $ver = $keymanVersion->getVersion($platform, $level);
    }

    if(!empty($ver)) {
      $ver_array = explode('.', $ver);
      if(count($ver_array) == 3) {
        $build = $ver_array[2];
      }
    }

    if(!empty($build)) {
      if(version_compare($ver, '3.0') > 0) $build = 469;
      header('Content-Type: text/plain');
      echo $build;
    } else {
      fail("$platform engine version not found");
    }
    exit;
  }
  
  if(!empty($ver)) {
    $verdata = array('platform' => $platform, 'level' => $level, 'version' => $ver);
  } else {
    $verdata = array('platform' => $platform, 'level' => $level, 'error' => 'No version exists for given platform and level');
  }
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($verdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>