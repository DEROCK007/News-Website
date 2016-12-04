<!DOCTYPE html>
<html lang="en">
<head>
    <title>Content</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php

require 'databaselogin.php';

session_start();
date_default_timezone_set('America/Chicago');

$username = (string) @$_SESSION['username'];
$st_id = (int) @$_GET['st_id'];
$st_id = $mysqli->real_escape_string($st_id);
$_SESSION['ready2fixuser'] = NULL;
$_SESSION['token2'] = NULL;
$_SESSION['token3'] = NULL;
$_SESSION['ready2fix'] = NULL;


//visual head//////////////////////////////////////////////////////////////////////
echo '
  <div class="headtitle">&nbsp;&nbsp; CONTENT</div>
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
		$querystr_user= "select username, password from userdata where username='".$userinput."'";
		if ($resultcounter_login = $mysqli->query($querystr_user)) {
		$result_cnt_user = $resultcounter_login->num_rows;
		$resultcounter_login->close();
		}
		
		//fetch user data
		if($result_cnt_user > 0){
			$userexistchk = $mysqli->prepare("select username, password from userdata where username=?");
			if(!$userexistchk){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$userexistchk->bind_param('s', $userinput);
			$userexistchk->execute();
			$userexistchk->bind_result($username_db, $userpw_db);
			for ($idx = 0; $idx < $result_cnt_user; $idx++){		//this loop is not necessary in here, i wrote it for ref
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

//mainbox start//////////////////////////////////////////////////////////////////////
echo '<div class="mainbox">
        
        <br />';

if(!empty($st_id)){
	//fetch story
	$getstory = $mysqli->prepare("select st_id, user_id, title, link, text, keyword1, keyword2, keyword3, keyword4, keyword5, timestamp_1, timestamp_t, image from story where st_id=?");
	if(!$getstory){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$getstory->bind_param('i', $st_id);
	$getstory->execute();
	$getstory->bind_result($st_id_db, $user_id_db, $title_db, $link_db, $text_db, $k1, $k2, $k3, $k4, $k5, $timestamp_1_db, $timestamp_t_db, $image_db);
	if($getstory->fetch()){
		echo '<div class="contitle">';
		printf("%s</div>",htmlentities($title_db));
		echo '<div class="coninfo"><strong>By ';
		echo '<a href="mod3userprofile.php?showuser=';
		printf("%s", htmlentities($user_id_db));
		echo '">';
		printf("%s</a>",htmlentities($user_id_db));
		printf(" at %s ", htmlentities($timestamp_1_db));
		printf("</strong><br />Last modified at %s<br /></div>", date('m/d/Y H:i:s',strtotime($timestamp_t_db)));
		if(!empty($k1) || !empty($k2) || !empty($k3) || !empty($k4) || !empty($k5)){
			echo '<div class="conkw">Tags:&nbsp;';
			if(!empty($k1)){echo '<a href="mod3search.php?q=';printf("%s",htmlentities($k1));echo '">';printf("%s</a>",htmlentities($k1));echo '&nbsp;&nbsp;';}
			if(!empty($k2)){echo '<a href="mod3search.php?q=';printf("%s",htmlentities($k2));echo '">';printf("%s</a>",htmlentities($k2));echo '&nbsp;&nbsp;';}
			if(!empty($k3)){echo '<a href="mod3search.php?q=';printf("%s",htmlentities($k3));echo '">';printf("%s</a>",htmlentities($k3));echo '&nbsp;&nbsp;';}
			if(!empty($k4)){echo '<a href="mod3search.php?q=';printf("%s",htmlentities($k4));echo '">';printf("%s</a>",htmlentities($k4));echo '&nbsp;&nbsp;';}
			if(!empty($k5)){echo '<a href="mod3search.php?q=';printf("%s",htmlentities($k5));echo '">';printf("%s</a>",htmlentities($k5));echo '&nbsp;&nbsp;';}
			echo '</div>';
		}
		if(!empty($link_db)){echo '<br /><div class="conlink"><strong>More at: </strong><a href="';printf("%s",htmlentities($link_db));echo '">';printf("%s</a></div>",htmlentities($link_db));}
		if($image_db != NULL){
			echo '<br />
        <div class="conimg"><img src="';
			printf("%s", $image_db);
			echo '" alt="Story image"></div>';
		}
		
		echo '<div class="context">
        <br /> ';
		//escape text and replace ^br^ with br tag
		$escaped_text = htmlentities($text_db);
		$ready_text = str_replace("^br^", "<br />", $escaped_text);
		printf("%s <br /></div>", $ready_text);
		$author = $user_id_db;
		$getstory->close();
		

		//show related stories
		echo '<div class="conrelated">
        <br /><strong>Related Story: </strong><br />';
		$related_st_id = [];
		$related_cnt = 0;
		$related_st_id = getrelatedstory($k1, $related_st_id);
		$related_st_id = getrelatedstory($k2, $related_st_id);
		$related_st_id = getrelatedstory($k3, $related_st_id);
		$related_st_id = getrelatedstory($k4, $related_st_id);
		$related_st_id = getrelatedstory($k5, $related_st_id);		
		$related_st_id = array_unique($related_st_id);
		
		foreach($related_st_id as $key => $selfchk){
			if($selfchk == $st_id){
				unset($related_st_id[$key]);
			}
		}
		
		foreach($related_st_id as $related_id_list){
			$getrelatedtitle = $mysqli->prepare("select title from story where st_id=?");
			$getrelatedtitle->bind_param('i', $related_id_list);
			$getrelatedtitle->execute();
			$getrelatedtitle->bind_result($related_st_title);
			if($getrelatedtitle->fetch()){
				echo '<a href="mod3content.php?st_id=';
				printf("%s",htmlentities($related_id_list));
				echo '">';
				printf("%s",htmlentities($related_st_title));
				echo '</a><br />';
			}
			$getrelatedtitle->close();
			$related_cnt += 1;
			if($related_cnt > 10){break;}
		}
		if($related_cnt == 0){
			echo 'No related story found.';
		}
		echo '</div>
        <br />
        <br />';
		
		//new comment handler
		if(isset($_POST['newcom'])){
			if(!empty($username)){
				$newcom = (string) $_POST['newcom'];
				if($_SESSION['token'] !== $_POST['token']){
					die("Request forgery detected");
				}
				$putcomment = $mysqli->prepare("insert into comment (usr_id, text, sty_id) values (?, ?, ?)");
				if(!$putcomment){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$putcomment->bind_param('sss', $username, $newcom, $st_id);
				$putcomment->execute();
				$putcomment->close();
			}
		}
		
		//fetch comments
		$querystr_com = "select com_id from comment where sty_id='".$st_id."'";			//$st_id already escaped
		if ($resultcounter_com = $mysqli->query($querystr_com)) {
			$com_cnt = $resultcounter_com->num_rows;
		}
		$resultcounter_com->close();
		
		if ($com_cnt > 0){
			echo '<div class="conrelated">
          <strong>Comments:</strong>
          <br /><br />';
			$getcomment = $mysqli->prepare("select com_id, usr_id, text, ctime_t from comment where sty_id= ? order by com_id DESC");
			if(!$getcomment){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$getcomment->bind_param('i', $st_id);
			$getcomment->execute();
			$getcomment->bind_result($com_id_db, $user_id_db, $com_text_db, $raw_ctime_t_db);
			for ($idx = 0; $idx < $com_cnt; $idx++){		//may remove unnecessary items
				$getcomment->fetch();
				$com_id[$idx] = $com_id_db;
				$user_id[$idx] = $user_id_db;
				$com_text[$idx] = $com_text_db;
				$ctime_t[$idx] = date('m/d/Y H:i:s',strtotime($raw_ctime_t_db));
			}
			$getcomment->close();
			
			for ($idx = 0; $idx < $com_cnt; $idx++){
				printf("%s <br />",htmlentities($com_text[$idx]));
				printf("by %s at %s <br />", htmlentities($user_id[$idx]), htmlentities($ctime_t[$idx]));
				if($user_id[$idx] == $username){			//need username verification at destination
					echo '<a href="mod3recom.php?edit_com_id=';
					printf("%s", htmlentities($com_id[$idx]));
					echo '"><div class="listclickbox">Edit</div></a>';
					echo '<a href="mod3delcom.php?del_com_id=';
					printf("%s", htmlentities($com_id[$idx]));
					echo '"><div class="listclickbox">Delete</div></a>';
				}
				echo '<hr>';
			}
			echo '</div>';
		}
		
		//show comment form if signed in
		if(!empty($username)){
			echo '<br />
        <div class="conrelated">
              <form method="POST">';
			echo '		<input class="comment" type="text" name="newcom" placeholder="new comment" />';
			echo '		<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
			echo '		<input class="goto" type="submit" value="Submit" name="newcomsubmit" />';
			echo '</form>';
			echo '</div>';
		}
		
		echo '</div>';
		//mainbox end//////////////////////////////////////////////////////////////////////
		
		//right box start//////////////////////////////////////////////////////////////////////
		echo '<div class="rightbox">
        <br />';
		//user control
		if(!empty($username))
		{
			echo '<a href="mod3userindex.php">
          <div class="ewrtclickbox">Go to my story board</div>
        </a>';
			if($author == $username){
				echo '<a href="mod3repost.php?edit_st_id=';
				printf("%s", htmlentities($st_id));
				echo '"><div class="ewrtclickbox">Edit Story</div></a>';
				echo '<a href="mod3del.php?del_st_id=';
				printf("%s", htmlentities($st_id));
				echo '"><div class="ewrtclickbox">Delete Story</div></a>';
			}
			
				echo '<a href="mod3post.php">
          <div class="ewrtclickbox">Post new story</div>
        </a>
        
        <a href="mod3index.php">
          <div class="ewrtclickbox">Back to index</div>
        </a>
        
        <a href="mod3logout.php">
          <div class="ewrtclickbox">Log out</div>
        </a>';
		}
		else {
			//display login form
			echo '<form method="POST">
          <input class="smlogin" type="text" name="userinput" placeholder="User Name" /><br />
          <input class="smlogin" type="password" name="userinputpw" placeholder="Password" /><br />
          <input class="goto" type="submit" value="LOGIN" name="login" />
        </form>


        <a href="mod3signup.php">
          <div class="ewrtclickbox">Sign Up</div>
        </a>
        
        <a href="mod3index.php">
          <div class="ewrtclickbox">Back to index</div>
        </a>';
		}
		
		echo '</div>';
		//right box end//////////////////////////////////////////////////////////////////////
		
		
		echo '    </div>
    <br/><br/><br/><br/>
  </div>';
		//block end//////////////////////////////////////////////////////////////////////
		
	}
	else {
		$getstory->close();
		$mysqli->close();
		header("Location: mod3index.php");
		exit;
	}
	
	

}
else {
    header("Location: mod3index.php");
    exit;
}

function getrelatedstory($keyword, $related){
	require 'databaselogin.php';
	if (!empty($keyword)){
		$i = 0;
		$getrelated = $mysqli->prepare("select st_id from story where keyword1 like ? or keyword2 like ? or keyword3 like ? or keyword4 like ? or keyword5 like ? ORDER BY st_id DESC");
		$getrelated->bind_param('sssss', $keyword, $keyword, $keyword, $keyword, $keyword);
		$getrelated->execute();
		$getrelated->bind_result($related_st_id_db);
		while($getrelated->fetch() && $i <= 10){
			array_push($related, $related_st_id_db);
			$i += 1;
		}
		$getrelated->close();
	}
	return $related;
}

?>
</body>
</html>
