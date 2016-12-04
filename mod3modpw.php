<!DOCTYPE html>
<html lang="en">
<head>
	<title>Password Recoery</title>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();
$fixuser = @$_SESSION['ready2fixuser'];
$username = @$_SESSION['username'];
$token2 = @$_SESSION['token2'];
$itoken2 = @$_GET['token2'];
$token3 = @$_SESSION['token3'];
if(!empty($fixuser) && !empty($token2) && $token2 == $itoken2 && empty($username)){
	require 'databaselogin.php';
	if(empty($token3)){
	$token3 = substr(md5(rand()), 0, 10);
	$_SESSION['token3'] = $token3;
	}
	$fixuser = $mysqli->real_escape_string($fixuser);
	//check if user exist
	$querystr = "select username, password from userdata where username='".$fixuser."'";
	if ($resultcounter = $mysqli->query($querystr)) {
	$result_cnt = $resultcounter->num_rows;
	$resultcounter->close();
	}
		
	if($result_cnt == 0) {
		die("Recovering user name in session does not exist.");	
	}

	//gen a self-submitting form to check data before continue
	echo '  <div class="headtitle">&nbsp;&nbsp; MODIFY PASSWORD</div>
				<div class="headstrip"></div>
					<div class="bodyblock">
					  <div class="bodybox">
						<form method="POST">
						  <p>
							<input class="login" type="password" name="newfixuserpw" id="newuserpw" placeholder="Set up a password..." /><br />
							<input class="login" type="password" name="newfixuserpwc" id="newuserpwc" placeholder="Re-enter the password..." /><br />';
	echo '					<input type="hidden" name="token3" value="';printf("%s", htmlentities($token3));echo '" />';
	echo '					<input class="goto" type="submit" value="Change password" />
						  </p>
						</form>';
							
	if(isset($_POST['newfixuserpw']) && isset($_POST['newfixuserpwc'])){
		//sanitize according to FIEO
		$newfixuserpw = (string) $_POST['newfixuserpw'];
		$newfixuserpwc = (string) $_POST['newfixuserpwc'];
		
		$warningmsg = NULL;
		$result_cnt = 0;
		$newcontent = NULL;
		
		//verify token
		if($_SESSION['token3'] !== $_POST['token3']){
			die("Request forgery detected");
		}
		
		if($newfixuserpw == $newfixuserpwc){
			if(strlen($newfixuserpw) > 9  && strlen($newfixuserpw) < 31){			//chk length
				//modify password
				$newfixuserpw = password_hash($newfixuserpw, PASSWORD_DEFAULT);
				$moduser = $mysqli->prepare("UPDATE userdata SET password=? WHERE username=?");
				if(!$moduser){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$moduser->bind_param('ss', $newfixuserpw, $fixuser);
				$moduser->execute();
				$moduser->close();
				$mysqli->close();
				$_SESSION['ready2fixuser'] = NULL;
				$_SESSION['token2'] = NULL;
				$_SESSION['token3'] = NULL;
				session_destroy();
				header("Location: mod3index.php");
				exit;
			}
			else{$warningmsg="A valid user password should be 10 ~ 30 characters long";}
		}
		else{$warningmsg="Passwords do not match.";}
	}
	else {$warningmsg="All fields are required.";}
	
	if(!empty($warningmsg)){			//display warning msg
		printf("<p>%s</p>",htmlentities($warningmsg));
	}
	
	echo '				<div class="bodyboxsml">
							<ul>
							  <li>A valid user password should be 10 ~ 30 characters long. </li>
							  <li>The password is case sensitive.</li>
							</ul>
						  </div>';
	echo '			    </div>
	
		<a href="mod3index.php">
		  <div class="clickbox">Back to Index</div>
		</a>
		<br/><br/><br/><br/><br/>
	
	  </div>';
	
}
else {
	header("Location: mod3index.php");
	exit;
}
?>
</body>
</html>
