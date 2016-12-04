<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
require 'databaselogin.php';
session_start();

//load username
$username = (string) @$_SESSION['username'];
$showme = (string) @$_GET['showuser'];

//user jump to own story board
if(!empty($username)){
	if($username == $showme){
		$mysqli->close();
		header("Location: mod3userindex.php");
		exit;
	}
}

if(!empty($showme)){
	
	echo '
    <div class="headtitle">&nbsp;&nbsp; User Profile</div>
    <div class="headstrip"></div>


    <div class="noticetool">
      <form action="mod3search.php" method="GET">
        &nbsp;<input class="topsearch" type="text" name="q" placeholder="Search..." />
        <input class="goto" type="submit" value="SEARCH" name="search" />
      </form>
    </div>


    <div class="bodyblock">
      <div class="bodybox">
        <div class="mainbox">';
	
	//show all user stories
	$showme_q = $mysqli->real_escape_string($showme);
	$querystr_list = "select st_id from story where user_id='".$showme_q."' order by st_id DESC";
	if ($resultcounter_list = $mysqli->query($querystr_list)) {
		$list_cnt = $resultcounter_list->num_rows;
		$resultcounter_list->close();
	}
	
	if($list_cnt > 0){
		$getlist = $mysqli->prepare("select st_id, user_id, title, link, text, timestamp_1, image from story where user_id=? order by st_id DESC");
		if(!$getlist){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$getlist->bind_param('s', $showme_q);
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
			echo '<br />
            <div class="listtitle"><a href="mod3content.php?st_id=';
			printf("%s",htmlentities($st_id[$idx]));
			echo '">';
			printf("%s",htmlentities($title[$idx]));
			echo '</a></div>
			<div class="listinfo">';
			printf("By %s at %s</div>",htmlentities($user_id[$idx]), htmlentities($timestamp_1[$idx]));
			if($image[$idx] != NULL){
				echo '<div class="listimg"><img src="';
				printf("%s", $image[$idx]);
				echo '" alt="Story image" height="100" width="100"></div>
          
            <div class="listtext">';
			}
			else {echo '<div>';}
			printf("%s ... </div><br /><br />",htmlentities(get_words($text[$idx], 30)));
		}
	}
	else{
		echo "<p>No story to show.</p><br /><br /><br /><br /><br /><br /><br /><br />";
	}
	
	echo '</div>
	<div class="rightbox">
          <br />
          <br />';
		  
	if(!empty($username)){
		//user controll
		echo '<a href="mod3index.php">
            <div class="ewrtclickbox">Back to index</div>
          </a>
		  <a href="mod3post.php">
            <div class="ewrtclickbox">Post new story</div>
          </a>
          <a href="mod3logout.php">
            <div class="ewrtclickbox">Log out</div>
          </a>';
	}
	else {
		echo '<a href="mod3index.php">
            <div class="ewrtclickbox">Back to index</div>
          </a>';
	}
	
	echo '        </div>
		</div>
      <br/><br/><br/><br/>
    </div>
	<br />';
	
}
else {
	header("Location: mod3index.php");
	exit;
}

function get_words($sentence, $count = 20) {				//code copied from http://stackoverflow.com/questions/5956610/how-to-select-first-10-words-of-a-sentence
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
}

?>
</body>
</html>
