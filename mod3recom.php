<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Comment</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();
$username = (string) @$_SESSION['username'];
$_SESSION['edit_com_id'] = NULL;
if (!empty($username)){
    
	require 'databaselogin.php';
    $com_id = (string)$_GET['edit_com_id'];	//no need to escape since using prepare
	
    $gettargetcom = $mysqli->prepare("select com_id, usr_id, text, sty_id from comment where com_id=?");
	if(!$gettargetcom){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$gettargetcom->bind_param('i', $com_id);
	$gettargetcom->execute();
	$gettargetcom->bind_result($com_id_db, $usr_id_db, $text_db, $sty_id_db);
	if($gettargetcom->fetch()){
		if($username == $usr_id_db){
			$_SESSION['edit_com_id'] = $com_id_db;
			echo '  <div class="headtitle">&nbsp;&nbsp; Edit Comment</div>
  <div class="headstrip"></div>
  <div class="bodyblock">
    <div class="bodybox">
      
      
      <br />
			<form action="mod3recoming.php" method="POST">
						  <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
						  
			echo '		<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
			
			echo '		<input class="posttitle" type="text" name="com" value="';
			printf("%s", htmlentities($text_db));
			echo '" /><br />';
			echo '<input class="goto" type="submit" value="SUBMIT" name="submit" />
						</form>
						      
      <br/><br/><br/><br/>
    
      <a href="mod3index.php">
        <div class="clickbox">Back to index</div>
      </a>
      
    </div>
    <br/><br/><br/><br/>
  </div>


  <br />';
		}
		else{
			$gettargetcom->close();
			$mysqli->close();
			header("Location: mod3index.php");
			exit;
		}
	}
	else{
			$gettargetcom->close();
			$mysqli->close();
			header("Location: mod3index.php");
			exit;
		}

	$gettargetcom->close();
	$mysqli->close();
	
}
else {
    header("Location: mod3index.php");
    exit;
}

?>
</body>
</html>
