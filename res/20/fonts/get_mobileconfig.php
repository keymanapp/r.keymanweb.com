<?php
  require_once('util.php');
  if(!isset($_REQUEST['id'])) {
    header("Location: keyman://localhost/open");
    flush();
    exit;
  }
  
  if(!preg_match('/^[a-zA-Z0-9_.-]+\.mobileconfig$/', $_REQUEST['id'])) {
    header("Location: keyman://localhost/open");
    flush();
    exit;
  }
  
  if(!file_exists($_REQUEST['id'])) {
    header("Location: keyman://localhost/open");
    flush();
    exit;
  }  
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" name="viewport">
    <title>Install Keyman Font</title>
    <style>

      body {
          text-align: center;
      }
      
      #installing .installed {
        display: none;
      }
      
      #installed .installing {
        display: none;
      }
      
      a {
          background: url("keyman-88.png") no-repeat scroll 24px 12px #EEEEEE;
          border: 1px solid #CCCCCC;
          border-radius: 4px;
          box-shadow: 4px 4px 8px rgba(128, 128, 128, 0.5);
          display: inline-block;
          height: 100px;
          width: 300px;
          text-align: left;
          font: bold 10pt Helvetica;
          text-decoration: none;
          color: black;
          padding: 4px 0 0 4px;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
      }    
      
      div {
          font: 16pt Helvetica;
          margin: 48px 12px;
      }
 
    </style>
    <script>
      window.onload = function() {
        //window.onpagehide = function() { document.getElementsByTagName('body')[0].id = 'installed'; };
        window.setTimeout(function() { 
          window.setTimeout(function() { 
            document.getElementsByTagName('body')[0].id = 'installed';
          }, 4000);
          location.href = "<?PHP echo $_REQUEST['id']; ?>"; 
        }, 500); 
      }
    </script>
  </head>
  <body id='installing'>
    <div class='installing'>Please follow the prompts to install the font for all apps on your device.</div>
    <img class='installing' src='ajax-loader.gif' alt=''>
    <div class='installed'>The font has been installed for all apps on your device.</div>
    <a class='installed' href="keyman://localhost/open">Touch now to return to</a>
  </body>
</html>
