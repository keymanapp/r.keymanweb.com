<?php /*
  Name:             chinese_pinyin
  Copyright:        Copyright (C) 2005 Tavultesoft Pty Ltd.
  Documentation:    
  Description:      
  Create Date:      5 Mar 2010

  Modified Date:    5 Mar 2010
  Authors:          mcdurdin
  Related Files:    
  Dependencies:     

  Bugs:             
  Todo:             
  Notes:            
  History:          05 Mar 2010 - mcdurdin - Removed debug from error messages
*/

  require_once(dirname(__FILE__) . '/../../../servervars.php');

  $times = array();
  savetime('Start');
  
  //echo "test";
  //exit;
  
  function savetime($name)
  {
    //global $times;
    //$times[] = array($name, microtime(true));
  }
  
  header('Content-Type: application/javascript; charset=UTF-8');

  function mssql_quote($s)
  {
    $v = strpos($s, "\0");
    if($v !== FALSE)
    {
      $s = substr($s, 0, strpos($s, "\0"));
    }
    return str_replace("'", "''", $s);
  }
  
  if(isset($_GET['py'])) 
  {
    $py=$_GET["py"]; $id = 0;
    if(isset($_GET['id'])) $id=$_GET["id"];

    //global $localserver_database_server, $localserver_database_password, $localserver_database_user, $localserver_database_name; // from ../../../servervars.php
    
    $connection = sqlsrv_connect($localserver_database_server, array(
      'APP'=>'kmw_chinese_pinyin',
      'Database'=>$localserver_database_name,
      'LoginTimeout'=>50,
      'UID'=>$localserver_database_user,
      'PWD'=>$localserver_database_password)
    );

    if($connection === false)
    {
      echo "alert('Chinese keyboard failed to connect to database');"; //".var_dump(sqlsrv_errors(SQLSRV_ERR_ALL));
      exit;
    }

    savetime('Database connected');
    
    // find the selection choice for the matching string, if any
    $sql = "EXEC KMW_Chinese_Search1 '".mssql_quote($py)."';";
    //$sql = "SELECT PinyinKey,ChineseText,Tip FROM KMW_Chinese_Pinyin WHERE (PinyinKey='" . mssql_quote($py) ."') ORDER BY Frequency DESC;";
    $rs = sqlsrv_query($connection,$sql);
    if($rs === false)
    {
      echo "alert('Chinese keyboard failed to query database');"; //".var_dump(sqlsrv_errors(SQLSRV_ERR_ALL));
      exit;
    }
    
    savetime('Query 1 run');

    $t = ''; $u = '';
    $tt = '[';
    while(($row = sqlsrv_fetch_array($rs)))
    {
      $t = $t . $tt . "'" . $row[1] . "'";
      $u = $u . $tt . "'" . $row[2] . "'";
      $tt = ',';
    }
    if($t == '') $t = '[]'; else $t .= ']';
    if($u == '') $u = '[]'; else $u .= ']';
    
    sqlsrv_next_result($rs);
    //sqlsrv_free_stmt($rs);

    savetime('Query 1 output');
    
    // also find first 20 selection choices for longer strings that match
    //$sql = "EXE KMW_Chinese_Search2 '".mssql_quote($py).
    //$sql = "SELECT TOP 20 PinyinKey,ChineseText,Tip FROM KMW_Chinese_Pinyin WHERE ((PinyinKey LIKE '" . mssql_quote($py) ."%') AND (PinyinKey<>'" . mssql_quote($py) . "')) ORDER BY Frequency DESC;";
    //$rs = sqlsrv_query($connection,$sql);

    savetime('Query 2 run');

    $t1 = ''; $u1 = '';
    $tt = '[';     
    while(($row = sqlsrv_fetch_array($rs))) 
    {       
        $t1 = $t1 . $tt . "'" . $row[1] . "'";
        $u1 = $u1 . $tt . "'" . $row[2] . "'";
        $tt = ',';
    }
    if($t1 == '') $t1 = '[]'; else $t1 .= ']';
    if($u1 == '') $u1 = '[]'; else $u1 .= ']';
    
    sqlsrv_free_stmt($rs);

    savetime('Query 2 output');
    
    echo "Keyboard_chinese_obj.showCandidates(" . $id . ",'$py'," . $t . "," . $t1 . "," . $u . "," . $u1 . ");";
    //echo "\n//document.getElementById('debug').innerHTML = '";
    
    savetime('End');
    /*
    $realst = $st = $times[0][1];
    
    foreach($times as $b)
    {
      echo $b[0] . ": " . ($b[1] - $st) . "\\n\n//";
      $st = $b[1];
    }
    echo "TOTAL: " . ($st - $realst) . "\\n\n//";
    echo "//'\n";*/
  }    
?>
