<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up Successful</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php			//a page to display sign up info and provide option for user to goto index or logout
    session_start();
	$username = (string) @$_SESSION['username'];
	if(!empty($username)){			//security check
		echo '
		<div class="headtitle">&nbsp;&nbsp; SIGN UP SUCCESSFUL</div>
		<div class="headstrip"></div>
		<div class="bodyblock">
		<div class="bodybox">';
		printf("<p>Congratulations, you have successfully registered as<strong> %s </strong></p>", htmlentities($username));
        echo '</div>
			  <form method="POST">
              <input class="goto" type="submit" value="Go to index" name="index"/>
              <input class="goto" type="submit" value="Logout" name="logout"/>
              </form>
			  <br/><br/><br/><br/><br/><br/>
			  </div>';
        if(isset($_POST['index'])){
            header("Location: mod3index.php");
            exit;
        }
        if(isset($_POST['logout'])){
            header("Location: mod3logout.php");
            exit;
        }
	}
	else{
        header("Location: mod3index.php");
        exit;
    }
?>
</body>
</html>
