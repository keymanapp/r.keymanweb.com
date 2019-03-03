<?php /*
  Name:             japanese
  Copyright:        Copyright (C) 2005 Tavultesoft Pty Ltd.
  Documentation:    
  Description:      
  Create Date:      19 Feb 2010

  Modified Date:    5 Mar 2010
  Authors:          mcdurdin
  Related Files:    
  Dependencies:     

  Bugs:             
  Todo:             
  Notes:            
  History:          19 Feb 2010 - jmdurdin - Initial version
                    05 Mar 2010 - mcdurdin - Initial publish; remove unused code and debug
                    12 Mar 2010 - jmdurdin - Remove duplicate kanji from selected list
*/
  
  header('Content-Type: application/javascript; charset=UTF-8');
  
	require_once(dirname(__FILE__) . '/../../../servervars.php');
  
  if(isset($_GET['kana'])) 
  {
    $kana=urldecode($_GET["kana"]); $id = 0;
    if(isset($_GET['id'])) $id=$_GET["id"];

    $connection = sqlsrv_connect($localserver_database_server, array(
      'APP'=>'kmw_japanese',
      'Database'=>$localserver_database_name,
      'LoginTimeout'=>50,
      'UID'=>$localserver_database_user,
      'PWD'=>$localserver_database_password
    ));  /* live site */
    if($connection === false)
    {
      echo "alert('Japanese keyboard failed to connect to database');"; // ".var_dump(sqlsrv_errors(SQLSRV_ERR_ALL))."');";
      exit;
    }

    // find the selection choice for the matching string, if any
    $k16 = iconv("utf-8","utf-16le",$kana);
    $params = array(array($k16,null,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY)));    
    
    $sql = "SELECT DISTINCT kanji,gloss,pri FROM KMW_Japanese WHERE (kana=?) ORDER BY pri;";
    
    $rs = sqlsrv_query($connection,$sql,$params);
    if($rs === false)
    {
      echo "alert('Japanese keyboard failed to query');"; // ".var_dump(sqlsrv_errors(SQLSRV_ERR_ALL))."');";
      exit;
    }

    $t = ''; $u = '';
    $tt = '[';

    while(sqlsrv_fetch($rs))
    {
      $kanji = sqlsrv_get_field($rs,0,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY));
      $gloss = sqlsrv_get_field($rs,1);
      $kanji = iconv("utf-16le","utf-8",$kanji);
      $gloss = str_replace("'","\'",$gloss);
      $t = $t . $tt . "'" . $kanji . "'";
      $u = $u . $tt . "'" . $gloss . "'";
      $tt = ',';
    }
    if($t == '') $t = '[]'; else $t .= ']';
    if($u == '') $u = '[]'; else $u .= ']';
    
    sqlsrv_free_stmt($rs);
    
    $k16a = iconv("utf-8","utf-16le",$kana . '%');
    $params = array(
      array($k16a,null,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY)),    
      array($k16,null,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY))
      );    

    // also find first 20 selection choices for longer strings that match
    $sql = "SELECT DISTINCT TOP 20 kanji,gloss,pri FROM KMW_Japanese WHERE ((kana LIKE ?) AND (kana<>?)) ORDER BY pri;";
    $rs = sqlsrv_query($connection,$sql,$params);
    $t1 = ''; $u1 = '';
    $tt = '[';     
    while(sqlsrv_fetch($rs)) 
    {       
      $kanji = sqlsrv_get_field($rs,0,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY));
      $gloss = sqlsrv_get_field($rs,1);
      $kanji = iconv("utf-16le","utf-8",$kanji);
      $gloss = str_replace("'","\'",$gloss);
      $t1 = $t1 . $tt . "'" . $kanji . "'";
      $u1 = $u1 . $tt . "'" . $gloss . "'";
      $tt = ',';
    }
    if($t1 == '') $t1 = '[]'; else $t1 .= ']';
    if($u1 == '') $u1 = '[]'; else $u1 .= ']';
    
    sqlsrv_free_stmt($rs);
    
    echo "Keyboard_japanese_obj.showCandidates(" . $id . ",'$kana'," . $t . "," . $t1 . "," . $u . "," . $u1 . ");";
  }    
?>
