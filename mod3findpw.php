<!DOCTYPE html>
<html lang="en">
<head>
	<title>Password Recovery</title>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();
$username = (string) @$_SESSION['username'];
require 'databaselogin.php';

//modify password
if(!empty($username)){
		
	$userinput = $mysqli->real_escape_string($username);
	//check if user exist
	$querystr = "select username, password from userdata where username='".$userinput."'";
	if ($resultcounter = $mysqli->query($querystr)) {
	$result_cnt = $resultcounter->num_rows;
	$resultcounter->close();
	}
	
	//fetch user data
	if($result_cnt > 0){
		$userexistchk = $mysqli->prepare("select username, password, q1, a1, q2, a2, q3, a3 from userdata where username=?");
		if(!$userexistchk){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$userexistchk->bind_param('s', $userinput);
		$userexistchk->execute();
		$userexistchk->bind_result($username_db, $userpw_db, $q1, $a1, $q2, $a2, $q3, $a3);
		$userexistchk->fetch();
		$userexistchk->close();
	}	
	else{
		die("User name in session does not exist.");	
	}

	//gen a self-submitting form to check data before continue
	echo '    <div class="headtitle">&nbsp;&nbsp; MODIFY PASSWORD</div>
				<div class="headstrip"></div>
					<div class="bodyblock">
					  <div class="bodybox">
						<form method="POST">
						  <p>
							<input class="login" type="password" name="oldfixuserpw" id="olduserpw" placeholder="Old password..." /><br />
							<input class="login" type="password" name="newfixuserpw" id="newuserpw" placeholder="Set up a password..." /><br />
							<input class="login" type="password" name="newfixuserpwc" id="newuserpwc" placeholder="Re-enter the password..." /><br />';
	echo '					<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
	echo '					<input class="goto" type="submit" value="Change password" />
						  </p>
						</form>';
							
	if(isset($_POST['oldfixuserpw']) && isset($_POST['newfixuserpw']) && isset($_POST['newfixuserpwc'])){
		//sanitize according to FIEO
		$oldfixuserpw = (string) $_POST['oldfixuserpw'];
		$newfixuserpw = (string) $_POST['newfixuserpw'];
		$newfixuserpwc = (string) $_POST['newfixuserpwc'];
		
		$warningmsg = NULL;
		$result_cnt = 0;
		$newcontent = NULL;
		
		//verify token
		if($_SESSION['token'] !== $_POST['token']){
			die("Request forgery detected");
		}
		
		if($newfixuserpw == $newfixuserpwc){
			if(strlen($newfixuserpw) > 9  && strlen($newfixuserpw) < 31){			//chk length
				if (hash_equals($userpw_db, crypt($oldfixuserpw, $userpw_db))) {
					//modify password
					$newfixuserpw = password_hash($newfixuserpw, PASSWORD_DEFAULT);
					$moduser = $mysqli->prepare("UPDATE userdata SET password=? WHERE username=?");
					if(!$moduser){
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					$moduser->bind_param('ss', $newfixuserpw, $username);
					$moduser->execute();
					$moduser->close();
					$mysqli->close();
					header("Location: mod3index.php");
					exit;
				}
				else{$warningmsg="The user name and password you entered do not match our record, please try again.";}
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

//find password
else {
	//gen form to get username
	if(empty(@$_SESSION['ready2fix'])){$_SESSION['ready2fix'] = FALSE;}		//initialize when first loaded
	
	if(isset($_POST['fixuser']) || $_SESSION['ready2fix'] == TRUE){
		if(isset($_POST['fixuser']) ){$userinput = $mysqli->real_escape_string((string)$_POST['fixuser']);}
		else{$userinput = $mysqli->real_escape_string((string)@$_POST['fixusername']);}
		//check if user exist
		$querystr = "select username, password from userdata where username='".$userinput."'";
		if ($resultcounter = $mysqli->query($querystr)) {
		$result_cnt = $resultcounter->num_rows;
		$resultcounter->close();
		}
		
		//fetch user data
		if($result_cnt == 1){
			$_SESSION['ready2fix'] = TRUE;
			$userexistchk = $mysqli->prepare("select username, password, q1, a1, q2, a2, q3, a3 from userdata where username=?");
			if(!$userexistchk){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$userexistchk->bind_param('s', $userinput);
			$userexistchk->execute();
			$userexistchk->bind_result($username_db, $userpw_db, $q1, $a1, $q2, $a2, $q3, $a3);
			$userexistchk->fetch();
			$userexistchk->close();
		}	
		else{
			echo "User does not exist.";
			$_SESSION['ready2fix'] = FALSE;
		}
	}
	
	if(!$_SESSION['ready2fix']){
		echo '  <div class="headtitle">&nbsp;&nbsp; PASSWORD RECOVERY</div>
			<div class="headstrip"></div>
				<div class="bodyblock">
				  <div class="bodybox">
				    <form method="POST">
				      <p>
				        <input class="login" type="text" name="fixuser" id="userin" placeholder="Enter your username..." /><br />
				        <input class="goto" type="submit" value="SUBMIT" />
				      </p>
				    </form>';
	}
	else {
		echo '  <div class="headtitle">&nbsp;&nbsp; PASSWORD RECOVERY</div>
					<div class="headstrip"></div>
						<div class="bodyblock">
						  <div class="bodybox">
							<form method="POST"><br />';
		switch($q1){
			case 1: echo 'What is your favourite color?'; break;
			case 2: echo 'What is your favourite food?'; break;
			case 3: echo 'What is the name of your first teacher?'; break;
			case 4: echo 'What is your favourite IDE?'; break;
			case 5: echo 'What is your call sign?'; break;
			default: echo 'What is your favourite color?'; break;
		}
		echo '<br />
								<input class="login" type="text" name="a1" placeholder="Answer for question 1..." /><br /><br />';
		switch($q2){
			case 1: echo 'Who is your favourite baseball player?'; break;
			case 2: echo 'Who is your favourite scientist?'; break;
			case 3: echo 'What is your favourite satellite?'; break;
			case 4: echo 'What is your favourite OS?'; break;
			case 5: echo 'In what year did you buy your first car?'; break;
			default: echo 'Who is your favourite baseball player?'; break;
		}
		echo '<br />
								<input class="login" type="text" name="a2" placeholder="Answer for question 2..." /><br /><br />';
		switch($q3){
			case 1: echo 'What is your favourite time zone?'; break;
			case 2: echo 'Who is your favourite artist?'; break;
			case 3: echo 'What is the name of your first pet?'; break;
			case 4: echo 'What is the rim size of your car?'; break;
			case 5: echo 'Do you prefer Jet A or Jet B?'; break;
			default: echo 'What is your favourite time zone?'; break;
		}						
		echo '<br />
								<input class="login" type="text" name="a3" placeholder="Answer for question 3..." /><br /><br />';
		echo '					<input type="hidden" name="fixusername" value="';printf("%s", htmlentities($userinput));echo '" />';
		echo '					<input class="goto" type="submit" value="Find password" />';
		echo '				</form>';
		
		
		if(isset($_POST['a1']) && isset($_POST['a2']) && isset($_POST['a3'])){
				$u_a1 = (string) $_POST['a1'];
				$u_a2 = (string) $_POST['a2'];
				$u_a3 = (string) $_POST['a3'];
				
				$warningmsg = NULL;
				$result_cnt = 0;
				$newcontent = NULL;
				
				if(hash_equals($a1, crypt($u_a1, $a1)) && hash_equals($a2, crypt($u_a2, $a2)) && hash_equals($a3, crypt($u_a3, $a3))){
					$_SESSION['ready2fixuser'] = $username_db;
					$token2 = substr(md5(rand()), 0, 10);
					$_SESSION['token2'] = $token2;
					$_SESSION['ready2fix'] = NULL;
					$addr = "Location: mod3modpw.php?token2=".$token2;
					header($addr);
					exit;
				}
				else {
					$warningmsg = "Security question answer incorrect.";
				}
		}
		else {
			$warningmsg = "All fields are required";
		}
		if(!empty($warningmsg)){			//display warning msg
				printf("<p>%s</p>",htmlentities($warningmsg));
		}
		
	}
	
	echo '			    </div>
	
		<a href="mod3index.php">
		  <div class="clickbox">Back to Index</div>
		</a>
		<br/><br/><br/><br/><br/>
	
	  </div>';
	
}
?>
</body>
</html>
