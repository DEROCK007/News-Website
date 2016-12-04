<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php

require 'databaselogin.php';

session_start();
$username = (string) @$_SESSION['username'];
$q = (string) @$_GET['q'];
$q = $mysqli->real_escape_string($q);

if(!empty($username)){
	if(!empty($q)){
		echo '  <div class="headtitle">&nbsp;&nbsp; SEARCH</div>
  <div class="headstrip"></div>
  <div class="noticetool">
    <form action="mod3mysearch.php" method="GET">
      &nbsp;<input class="topsearch" type="text" name="q" placeholder="Search my stories..." />
      <input class="goto" type="submit" value="SEARCH" name="search" />
    </form>
  </div>
  <div class="bodyblock">
    <div class="bodybox">
      <div class="mainbox">
      <div class="listtitle">Search Result</div>';
	  
	  //allow fuzzy search
		$related_st_id = [];
		$related_cnt = 0;
		$related_st_id = getmyrelatedstory($q, $related_st_id, $username);           //notice that i only allow 100- results
		$related_st_id = getmysimtitle($q, $related_st_id, $username);           //notice that i only allow 100- results
		$related_st_id = array_unique($related_st_id);
		
		foreach($related_st_id as $related_id_list){           
			$getrelatedtitle = $mysqli->prepare("select title, text from story where st_id=?");
			$getrelatedtitle->bind_param('i', $related_id_list);
			$getrelatedtitle->execute();
			$getrelatedtitle->bind_result($related_st_title, $related_text);
			if($getrelatedtitle->fetch()){
				echo '<br />
          <div class="listtitle">
            <a href="mod3content.php?st_id=';
				printf("%s",htmlentities($related_id_list));
				echo '">';
				printf("%s",htmlentities($related_st_title));
				echo '</a></div>
			  <div>';
				printf("%s", htmlentities(get_words($related_text, 15).'...'));
				echo '</div><br />';
			}
			$getrelatedtitle->close();
			$related_cnt += 1;
			if($related_cnt > 200){break;}          //notice that i only allow 200- total results
		}
		if($related_cnt == 0){echo '<p>No result found.</p><br /><br /><br /><br /><br /><br /><br /><br />';}
		echo '</div>';
	
		echo '<div class="rightbox">
			<br />';
		echo '<div class="rightnotice">';
		printf("Hello, %s!", htmlentities($username));
		echo '</div><br />';
		echo '<a href="mod3index.php"><div class="ewrtclickbox">Back to index</div></a>
        <a href="mod3userindex.php">
          <div class="ewrtclickbox">Go to my story board</div>
        </a>
        <a href="mod3logout.php">
          <div class="ewrtclickbox">Log out</div>
        </a>
        <a href="mod3post.php">
          <div class="ewrtclickbox">Post new story</div>
        </a>';
		echo '      </div>
		</div>
		<br/><br/><br/><br/>
	  </div>';
	
	}
	else {          //no keyword provided
		header("Location: mod3index.php");
		exit;
	}
}
else {          //not logged in
	header("Location: mod3index.php");
	exit;
}

function getmyrelatedstory($keyword, $related, $user){
	require 'databaselogin.php';
	if (!empty($keyword)){
		$keyword = $keyword.'%';			//allow fuzzy suffix
		$i = 0;
		$getrelated = $mysqli->prepare("select st_id from story where user_id=? and ( keyword1 like ? or keyword2 like ? or keyword3 like ? or keyword4 like ? or keyword5 like ? ) ORDER BY st_id DESC");
		$getrelated->bind_param('ssssss', $user, $keyword, $keyword, $keyword, $keyword, $keyword);
		$getrelated->execute();
		$getrelated->bind_result($related_st_id_db);
		while($getrelated->fetch() && $i <= 100){
			array_push($related, $related_st_id_db);
			$i += 1;
		}
		$getrelated->close();
	}
	return $related;
}

function getmysimtitle($keyword, $related, $user){
	require 'databaselogin.php';
	if (!empty($keyword)){
		$keyword = '%'.$keyword.'%';
		$i = 0;
		$getrelated = $mysqli->prepare("select st_id from story where user_id=? and title like ? ORDER BY st_id DESC");
		$getrelated->bind_param('ss',$user, $keyword);
		$getrelated->execute();
		$getrelated->bind_result($related_st_id_db);
		while($getrelated->fetch() && $i <= 100){
			array_push($related, $related_st_id_db);
			$i += 1;
		}
		$getrelated->close();
	}
	return $related;
}

function get_words($sentence, $count = 15) {				//code copied from http://stackoverflow.com/questions/5956610/how-to-select-first-10-words-of-a-sentence
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
}

?>
</body>
</html>
