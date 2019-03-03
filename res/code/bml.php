<?php /*
  code/index.php: respond to a KeymanWeb query with a pre-generated response.  This response is refreshed from the primary server on a regular basis.

  History:          08 Jun 2010 - mcdurdin - Initial version
                    20 Aug 2010 - mcdurdin-admin - Record BML timecode to uniquify bookmarklet users so we can understand where they are using it
*/
  /*
  */
  ini_set('track_errors', 1);

  require_once('./sitesettings.php');
  require_once('./_timestamp.php');
  
  /*
    End of server variables
  */
  
  dotimestamp();
   
  ob_start();
  date_default_timezone_set('UTC');
  
  header("Content-Type: text/javascript; charset=UTF-8");
  
  // Date in the past
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  
  // always modified
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  
  // HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  
  // HTTP/1.0
  header("Pragma: no-cache");  
  
  /*
   * Check parameters for validity
   */ 

  dotimestamp('Finished header');
  
  function InvalidCall($code)
  {
    echo "alert('KeymanWeb: Invalid call to r.keymanweb.com - $code');";
    exit;
  }

  $domain = isset($_GET['domain']) ? strtolower($_GET['domain']) : '';
  if(substr($domain, 0, 4) == 'www.') $domain = substr($domain, 4);

  $page = isset($_GET['page']) ? $_GET['page'] : '';
  
  if(!isset($_GET['keyboard'])) InvalidCall('keyboard missing');
  $keyboard = $_GET['keyboard'];
  if(!preg_match('/^[a-zA-Z0-9_]+$/', $keyboard)) InvalidCall('Invalid keyboard name');
  
  $lang = isset($_GET['lang']) ? $_GET['lang'] : '';
  
  if(!isset($_GET['langid'])) InvalidCall('language id missing');
  $langid = $_GET['langid'];
  if(!preg_match('/^[a-z][a-z][a-z]$/', $langid)) InvalidCall('invalid language id');
  
  $debug = isset($_GET['debug']) && $_GET['debug'] === '1';
  
  dotimestamp('Loaded data');
  
  if(isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER']; else $referer = '';
  
  /*
   * Log the visit
   */

  // TODO: Log to Google Analytics
  
  dotimestamp('Finished logging visit');
  
  /*
   * Output keymanweb.js and load keyboard
   */ 

  // Locate current keymanweb.js (stable 2.0)

  require_once('./_get-version.php');
  $keymanVersion = new KeymanVersion();
  $ver = $keymanVersion->getVersion('web', 'stable');
  if(empty($ver)) {
    $ver = $keymanVersion->getVersion('web', 'beta');
    if(empty($ver)) {
      $ver = $keymanVersion->getVersion('web', 'alpha');
      if(empty($ver)) {
        echo "console.log('ERROR: KeymanWeb version not found');";
        exit;
      }
    }
  }
    
  function getutf8($s) {
    if(substr($s, 0, 3) == chr(0xEF).chr(0xBB).chr(0xBF)) return substr($s, 3);
    return $s;
  }

  $ver_array = explode('.', $ver);
  $build = $ver_array[2];
  
  // Read the KeymanWeb code from s.keyman.com/
  $KeymanWebRoot = "{$KeymanCloudRootPath}kmw\\engine\\{$ver}";
  
  //
  // Note: we need at least version 2.0.465
  //
  
  if(intval($build) < 465) {
    echo "console.log('KeymanWeb ($ver) build $build is lower than 465. This may cause KeymanWeb to fail.');";
  }

  if(intval($ver_array[0]) >= 10) {
    $kmwbase = "keyman";
  } else {
    $kmwbase = "tavultesoft.keymanweb";
  }
 
  if($debug) {
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwstring.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwbase.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\keymanweb.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwosk.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwnative.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwcallback.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwkeymaps.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwlayout.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwinit.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\src\\kmwuitoggle.js"));
  } else {
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\keymanweb.js"));
    echo getutf8(file_get_contents("{$KeymanWebRoot}\\kmwuitoggle.js"));
  }
  
  echo <<<END
(function() {
  //console.log('path');
  $kmwbase.init({
    root: "//{$StaticResourceDomain}/kmw/engine/{$build}/", 
    resources: "//{$StaticResourceDomain}/kmw/engine/{$build}/", 
    keyboards: "//{$StaticResourceDomain}/keyboard/",
    ui: "toggle"
  });
  $kmwbase.addKeyboards("$keyboard@$langid");
})();
END;

  dotimestamp('Finished output');
  
  reviewtimestamp();
  
  ob_end_flush();
?>
