<?php /*
  Name:             sitesettings
  Copyright:        Copyright (C) 2005 Tavultesoft Pty Ltd.
  Documentation:    
  Description:      
  Create Date:      19 Feb 2010

  Modified Date:    8 Jun 2010
  Authors:          mcdurdin, mcdurdin-admin
  Related Files:    
  Dependencies:     

  Bugs:             
  Todo:             
  Notes:            
  History:          19 Feb 2010 - mcdurdin - Make KeymanWebSiteName use consistent
                    08 Jun 2010 - mcdurdin - Add keyboards root and bookmarklet subscription ID
*/

  $site_url = 'r.keymanweb.com';

  // We allow the site to strip off everything post its basic siteurl 
  
  function GetHostSuffix() {
    global $site_url;
    $name = $_SERVER['SERVER_NAME'];
    if(stripos($name, $site_url.'.') == 0) {
      return substr($name, strlen($site_url), 1024);
    }
    return '';
  }
  
  $site_suffix = GetHostSuffix();

  if(isset($_ENV['S_KEYMAN_COM'])) {
    $KeymanCloudRootPath = $_ENV['S_KEYMAN_COM'] . "/";
  } else {
    $KeymanCloudRootPath = $_SERVER["DOCUMENT_ROOT"] . "/../../s.keyman.com/";
  }

  if(!file_exists($KeymanCloudRootPath . 'kmw/engine/')) {
    $KeymanCloudRootPath = 'd:\\domains\\tavultesoft.com\\s.keyman.com\\';
    if(!file_exists($KeymanCloudRootPath . 'kmw/engine/')) {
      die('Could not find s.keyman.com local instance at '.$KeymanCloudRootPath);
    }
  }
  
  if($site_suffix == '') {
    $TestServer = false;

    $KMWDataRoot = "d:\\domains\\keymanweb.com\\sitedata\\";
    $KMWKeyboardsRoot = "d:\\domains\\keymanweb.com\\site\\res\\kmw\\";
    $KeymanCloudRootPath = "d:\\domains\\tavultesoft.com\\s.keyman.com\\";
    $StaticResourceDomain = 's.keyman.com';
    $SecureStaticResourceDomain = 's.keyman.com';

    $KMWBookmarkletID = 349;
    $KMWBookmarklet20ID = 995;
  } else {
    $TestServer = true;
    
    $KMWDataRoot = null; //"e:\\data\\tavultesoft\\keymanweb\\sitedata\\";
    $KMWKeyboardsRoot = null; //"e:\\data\\tavultesoft\\keymanweb\\site\\res\\kmw\\";
    $KMWBookmarkletID = 349;
    $KMWBookmarklet20ID = 539;
  }
  
  $StaticResourceDomain = "s.keyman.com{$site_suffix}";
  $SecureStaticResourceDomain = $StaticResourceDomain;
  $KeymanWebSiteName = $site_url;
?>
