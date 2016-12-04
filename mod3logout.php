<!DOCTYPE html>
<html lang="en">
<head>
	<title>Logoff</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
	<meta http-equiv="refresh" content="2; url=mod3index.php"> 
</head>
<body class="common">

<?php
	session_start();
    session_destroy();
	echo '  <div class="headtitle">&nbsp;&nbsp; LOG OFF</div>
  <div class="headstrip"></div>
  <div class="bodyblock">
    <div class="noticesta">
      <p>You have logged off.</p>
      <p>Redirecting to Index...</p>
      <br/>
      <div class="spinbox">+</div>
      <br/><br/><br/>
    </div>
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
  </div>';
	exit;			//jump to index page after session destory
?>

</body>
</html>
