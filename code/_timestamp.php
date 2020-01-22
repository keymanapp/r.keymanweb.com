<?php
  $timestamplog = '';
  function dotimestamp($title = '')
  {
    /*global $initialtimestamp, $currenttimestamp, $timestamplog;
    $timestamp = microtime(true);
    if(isset($currenttimestamp))
    {
      $timestamplog .= $title . ": ".sprintf("%0.6f", ($timestamp - $currenttimestamp))."\n";
    }
    else $initialtimestamp = $timestamp;
    $currenttimestamp = microtime(true);*/
  }
  function reviewtimestamp()
  {
    /*global $initialtimestamp, $currenttimestamp, $timestamplog;
    $timestamp = microtime(true);
    $timestamplog .= "TOTAL: ".sprintf("%0.6f", ($timestamp - $initialtimestamp))."\n";
    echo "\n\n/* *****************\n\n$timestamplog\n\n***************** *"."/\n\n";*/
  }
?>