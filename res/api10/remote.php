<?php
  // Proxy a file across from a remote site.  Used only for JSON 
  // Later: parse the file and ...
  
  function fail($code, $message='') {
    if($message == '') $message = 'Fail';
    header($_SERVER["SERVER_PROTOCOL"]." $code $message");
    exit(1);
  }

  fail(503, 'The remote API is no longer active.');
  
  if(!isset($_GET['url'])) {
    fail(400, 'Invalid parameters');
  }
  
  $url = $_GET['url'];
  
  if(!preg_match('/^http(s)?:\/\//', $url)) {
    fail(400,'Invalid URL');
  }
  
  // Request UTF-8 character set when getting file contents
  $opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
  $context = stream_context_create($opts);

  $content = @file_get_contents($url, false, $context);
  
  if($content === null || $content === false) {
    fail(400,'Unable to download');
  }
  
  if(!empty($php_errormsg)) {
    fail(400,'Unable to download: '+$php_errormsg);
  }

  // Strip BOM from start of content
  $content = ltrim($content, "\xEF\xBB\xBF");
  
  $content_data = @json_decode($content);
  
  if($content_data == null) {
    fail(400,'Invalid content');
  }
  
  // TODO: validate content for standard format, proxying, caching
  
  echo $content;
?>