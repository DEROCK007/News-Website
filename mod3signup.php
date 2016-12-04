<!DOCTYPE html>
<html lang="en">
<head>
	<title>Sign Up</title>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();
$username = (string) @$_SESSION['username'];
$_SESSION['ready2fixuser'] = NULL;
$_SESSION['token2'] = NULL;
$_SESSION['token3'] = NULL;
$_SESSION['ready2fix'] = NULL;
if(empty($username)){
			//gen a self-submitting form to check data before continue
	
	echo '  <div class="headtitle">&nbsp;&nbsp; SIGN UP</div>
				<div class="headstrip"></div>
					<div class="bodyblock">
					  <div class="bodybox">
						<form method="POST">
						  <p>
							<input class="login" type="text" name="newuser" id="userin" placeholder="Create an username..." /><br />
							<input class="login" type="password" name="newuserpw" id="userpw" placeholder="Set up a password..." /><br />
							<input class="login" type="password" name="newuserpwc" id="userpwc" placeholder="Re-enter the password..." /><br />
							<br />
							<select class="login" name="q1">
								<option value="1">What is your favourite color?</option>
								<option value="2">What is your favourite food?</option>
								<option value="3">What is the name of your first teacher?</option>
								<option value="4">What is your favourite IDE?</option>
								<option value="5">What is your call sign?</option>
							</select><br />
							<input class="login" type="text" name="a1" placeholder="Answer for question 1..." /><br />
							<br />
							<select class="login" name="q2">
								<option value="1">Who is your favourite baseball player?</option>
								<option value="2">Who is your favourite scientist?</option>
								<option value="3">What is your favourite satellite?</option>
								<option value="4">What is your favourite OS?</option>
								<option value="5">In what year did you buy your first car?</option>
							</select><br />
							<input class="login" type="text" name="a2" placeholder="Answer for question 2..." /><br />
							<br />
							<select class="login" name="q3">
								<option value="1">What is your favourite time zone?</option>
								<option value="2">Who is your favourite artist?</option>
								<option value="3">What is the name of your first pet?</option>
								<option value="4">What is the rim size of your car?</option>
								<option value="5">Do you prefer Jet A or Jet B?</option>
							</select><br />
							<input class="login" type="text" name="a3" placeholder="Answer for question 3..." /><br />
							<input class="goto" type="submit" value="Sign up" />
						  </p>
						</form>';
	
	if(isset($_POST['newuser']) && isset($_POST['newuserpw']) && isset($_POST['newuserpwc']) && isset($_POST['q1']) && isset($_POST['q2']) && isset($_POST['q3']) && isset($_POST['a1']) && isset($_POST['a2']) && isset($_POST['a3'])){
		$newuser = (string) $_POST['newuser'];			//sanitize according to FIEO
		$newuserpw = (string) $_POST['newuserpw'];
		$newuserpwc = (string) $_POST['newuserpwc'];
		$q1 = (int) $_POST['q1'];
		$q2 = (int) $_POST['q2'];
		$q3 = (int) $_POST['q3'];
		$a1 = (string) $_POST['a1'];
		$a2 = (string) $_POST['a2'];
		$a3 = (string) $_POST['a3'];
		
		$warningmsg = NULL;
		$usercheck = NULL;
		$result_cnt = 0;
		$newcontent = NULL;
		
		if (preg_match('/^[A-Za-z0-9]+$/', $newuser)){			//throw any input with special chars, allow only A-Z, a-z, 0-9
			if($newuserpw == $newuserpwc){
				if(strlen($newuser) > 5 && strlen($newuser) < 16 && strlen($newuserpw) > 9  && strlen($newuser) < 31){			//chk length
					if(strlen($a1) <= 30  && strlen($a2) <= 30  && strlen($a3) <= 30){
						if($q1 > 0 && $q1 < 6 && $q2 > 0 && $q2 < 6 && $q3 > 0 && $q3 < 6){
							if(!empty($newuser)){							//throw case (if any) that may mess up the security chk
								if(!preg_match('/^[0]+$/', $newuser)){				//throw all zero cases coz i dont like 'em
									require 'databaselogin.php';
									$userinput = $mysqli->real_escape_string($newuser);
									if($userinput == $newuser){
										
										//check dupe username
										$querystr = "select username from userdata where username='".$userinput."'";
										if ($resultcounter = $mysqli->query($querystr)) {
											$result_cnt = $resultcounter->num_rows;
											$resultcounter->close();
										}
										if($result_cnt > 0){
											$warningmsg = "User already exists.";
										}
										else{
											$newuserpw = password_hash($newuserpw, PASSWORD_DEFAULT);
											$a1 = password_hash($a1, PASSWORD_DEFAULT);
											$a2 = password_hash($a2, PASSWORD_DEFAULT);
											$a3 = password_hash($a3, PASSWORD_DEFAULT);
											
											$putuser = $mysqli->prepare("insert into userdata (username, password, q1, a1, q2, a2, q3, a3) values (?, ?, ?, ?, ?, ?, ?, ?)");
											if(!$putuser){
												printf("Query Prep Failed: %s\n", $mysqli->error);
												exit;
											}
											$putuser->bind_param('ssssssss', $newuser, $newuserpw, $q1, $a1, $q2, $a2, $q3, $a3);
											$putuser->execute();
											$putuser->close();
											
											$create_cnt = 0;
											$querystr = "select username from userdata where username='".$userinput."'";
											if ($createchk = $mysqli->query($querystr)) {
												$create_cnt = $createchk->num_rows;
												$createchk->close();
											}
											
											if($create_cnt == 1){
												if(mkdir("userdata/".$newuser, 0755)){
													$_SESSION['username'] = $newuser;
													$_SESSION['token'] = substr(md5(rand()), 0, 10);
													$mysqli->close();
													header("Location: mod3signupok.php");
													exit;
												}
											}
											else{
												echo '<p>Database failure. <a href="mod3index.php">Back to index</a></p>';
											}
											
										}
									}
									else{$warningmsg="Invalid user name.";}
								}
								else{$warningmsg="Nope, all zero is not allowed.";}
							}
							else{$warningmsg="Nope, empty elements are not allowed.";}
						}
						else{$warningmsg="I know it's quirky but please use the questions we provided.";}
					}
					else{$warningmsg="Please consider some shorter answers.";}
				}
				else{$warningmsg="A valid user should be 6 ~ 15 characters long, 10 ~ 30 for password";}
			}
			else{$warningmsg="Passwords do not match.";}
		}
		else{$warningmsg="Nuh uh, letters and numbers only";}
	}
	else {$warningmsg="All fields are required.";}
	
	if(!empty($warningmsg)){			//display warning msg
		printf("<p>%s</p>",htmlentities($warningmsg));
	}
	
	echo '				<div class="bodyboxsml">
							<ul>
							  <li>User name should only consist of letters and numbers. </li>
							  <li>A valid user name should be 6 ~ 15 characters long. </li>
							  <li>A valid user password should be 10 ~ 30 characters long. </li>
							  <li>Please keep the answer of the security question with in 30 characters long. </li>
							  <li>The user name is case sensitive.</li>
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
