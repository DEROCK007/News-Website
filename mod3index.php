<!DOCTYPE html>
<html lang="en">
<head>
    <title>Index</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
require 'databaselogin.php';
session_start();

$_SESSION['ready2fixuser'] = NULL;
$_SESSION['token2'] = NULL;
$_SESSION['token3'] = NULL;
$_SESSION['ready2fix'] = NULL;

//load optional:username
$username = (string) @$_SESSION['username'];

//visual head//////////////////////////////////////////////////////////////////////
echo '
  <div class="headtitle">&nbsp;&nbsp; INDEX</div>
  <div class="headstrip"></div>';
//search bar
echo '  <div class="noticetool">
    <form action="mod3search.php" method="GET">
      &nbsp;<input class="topsearch" type="text" name="q" placeholder="Search..." />
      <input class="goto" type="submit" value="SEARCH" name="search" />
    </form>
  </div>';
//block start//////////////////////////////////////////////////////////////////////
echo '  <div class="bodyblock">
    <div class="bodybox">';

//login code
if (isset($_POST['userinput']) && isset($_POST['userinputpw']) && empty($username)){
	    $userinput = (string) $_POST['userinput'];
		$userinputpw = (string) $_POST['userinputpw'];
		
		//escape sql code
		$userinput = $mysqli->real_escape_string($userinput);
		$userinputpw = $mysqli->real_escape_string($userinputpw);
		
		//check if user exist
		$querystr = "select username, password from userdata where username='".$userinput."'";
		if ($resultcounter = $mysqli->query($querystr)) {
		$result_cnt = $resultcounter->num_rows;
		$resultcounter->close();
		}
		
		//fetch user data
		if($result_cnt > 0){
			$userexistchk = $mysqli->prepare("select username, password from userdata where username=?");
			if(!$userexistchk){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$userexistchk->bind_param('s', $userinput);
			$userexistchk->execute();
			$userexistchk->bind_result($username_db, $userpw_db);
			for ($idx = 0; $idx < $result_cnt; $idx++){		//this loop is not necessary in here, i wrote it for ref
				$userexistchk->fetch();
				$db_username[$idx] = $username_db;
				$db_userpw[$idx] = $userpw_db;
			}
			$userexistchk->close();
			if (hash_equals($db_userpw[0], crypt($userinputpw, $db_userpw[0]))) {
				$_SESSION['username'] = $userinput;
				$_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string
			}
			else{
				echo '<div class="noticesta">The user name and password you entered do not match our record, please try again.</div>';	
			}
		}	
		else{
			echo '<div class="noticesta">The user name and password you entered do not match our record, please try again.</div>';	
		}
}

//load optional:username
$username = (string) @$_SESSION['username'];


//mainbox start//////////////////////////////////////////////////////////////////////
echo '<div class="mainbox">
        
        <strong>New Stories</strong>';

//show lastest stories
$querystr_list = "select st_id from story order by st_id DESC";		//no need to escape
if ($resultcounter_list = $mysqli->query($querystr_list)) {
	$list_cnt = $resultcounter_list->num_rows;
	$resultcounter_list->close();
}

if ($list_cnt > 30){$list_cnt = 30;}		//adjust count to within 30

if($list_cnt > 0){
	$getlist = $mysqli->prepare("select st_id, user_id, title, link, text, timestamp_1, image from story order by st_id DESC limit ?");
	if(!$getlist){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$getlist->bind_param('i', $list_cnt);
	$getlist->execute();
	$getlist->bind_result($st_id_db, $user_id_db, $title_db, $link_db, $text_db, $timestamp_1_db, $image_db);
	for ($idx = 0; $idx < $list_cnt; $idx++){		//may remove unnecessary items
		$getlist->fetch();
		$st_id[$idx] = $st_id_db;
		$user_id[$idx] = $user_id_db;
		$title[$idx] = $title_db;
		$link[$idx] = $link_db;
		$text[$idx] = $text_db;
		$timestamp_1[$idx] = $timestamp_1_db;
		$image[$idx] = $image_db;
	}
	$getlist->close();
	for ($idx = 0; $idx < $list_cnt; $idx++){		//list results
		echo '
		<br />
            <div class="listtitle">
              <a href="mod3content.php?st_id=';
		printf("%s",htmlentities($st_id[$idx]));
		echo '">';
		printf("%s",htmlentities($title[$idx]));
		echo '</a></div>
          <div class="listinfo">
            &emsp;&mdash;By ';
		echo '<a href="mod3userprofile.php?showuser=';
		printf("%s", htmlentities($user_id[$idx]));
		echo '">';
		printf("%s</a>",htmlentities($user_id[$idx]));
		printf(" at %s", htmlentities($timestamp_1[$idx]));
		echo '
          </div>';
		if($image[$idx] != NULL){
			echo '			<div class="listimg">
            <img src="';
			printf("%s", $image[$idx]);
			echo '" alt="Story image" height="100" width="100"></div>';
		}
		echo '			<div class="listtext">';
		printf("%s ... <br />",htmlentities(get_words($text[$idx], 30)));
		echo '</div>';
	}
}
else{
	echo "<p>I can't believe it! No one wrote a single story?!</p><br /><br /><br /><br /><br /><br /><br /><br />";
}

echo '</div>';
//mainbox end//////////////////////////////////////////////////////////////////////

//right box start//////////////////////////////////////////////////////////////////////
echo '      <div class="rightbox">
        <br />';
//display login / user control
if (empty($username)){
    echo '<form method="POST">
          <input class="smlogin" type="text" name="userinput" placeholder="User Name" /><br />
          <input class="smlogin" type="password" name="userinputpw" placeholder="Password" /><br />
          <input class="goto" type="submit" value="LOGIN" name="login" />
        </form>';
	echo '<a href="mod3findpw.php">
          <div class="ewrtclickbox">Forget Password</div>
        </a>';
	echo '<a href="mod3signup.php">
          <div class="ewrtclickbox">Sign Up</div>
        </a>';
}
else{
	echo '<br />
        <div class="rightnotice">';
    printf("Hello, %s!</div>", htmlentities($username));
	echo '<br />
        <a href="mod3userindex.php">
          <div class="ewrtclickbox">Go to my story board</div>
        </a>
		<a href="mod3post.php">
          <div class="ewrtclickbox">Post new story</div>
        </a>
		<a href="mod3findpw.php">
          <div class="ewrtclickbox">Change Password</div>
        </a>
        <a href="mod3logout.php">
          <div class="ewrtclickbox">Log out</div>
        </a>';
}

echo '</div>';
//right box end//////////////////////////////////////////////////////////////////////


echo '    </div>
    <br/><br/><br/><br/>
  </div>';
//block end//////////////////////////////////////////////////////////////////////


$mysqli->close();

function get_words($sentence, $count = 20) {				//code copied from http://stackoverflow.com/questions/5956610/how-to-select-first-10-words-of-a-sentence
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
}
?>
</body>
</html>
