<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Story</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();
$username = (string) @$_SESSION['username'];
$_SESSION['edit_st_id'] = NULL;
if (!empty($username)){
    
	require 'databaselogin.php';
    $st_id = (string)$_GET['edit_st_id'];	//no need to escape since using prepare
	
    $getstory = $mysqli->prepare("select st_id, user_id, title, link, text, keyword1, keyword2, keyword3, keyword4, keyword5, timestamp_1, image from story where st_id=?");
	if(!$getstory){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$getstory->bind_param('i', $st_id);
	$getstory->execute();
	$getstory->bind_result($st_id_db, $user_id_db, $title_db, $link_db, $text_db, $k1, $k2, $k3, $k4, $k5, $timestamp_1_db, $image_db);
	if($getstory->fetch()){
		if($username == $user_id_db){
			$_SESSION['edit_st_id'] = $st_id_db;
				echo '  <div class="headtitle">&nbsp;&nbsp; Edit Story</div>
  <div class="headstrip"></div>
  <div class="bodyblock">
    <div class="bodybox">
      
      
      <br />
      
      <form enctype="multipart/form-data" action="mod3reposting.php" method="POST">

        <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
						  
			echo '		<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
			
			echo '			  <input class="posttitle" type="text" name="title" value="';
			printf("%s", htmlentities($title_db));
			echo '" /><br />';
			
			echo '			  <textarea class="posttext" name="story">';
			printf("%s", htmlentities($text_db));												//no need to convert ^br^ at this point
			echo '</textarea><br /><br />';
			
			echo '			  <input class="postattr" type="text" name="link" value="';
			printf("%s", htmlentities($link_db));
			echo '" /><br /><br />';
			
			echo '			  <input class="postshort" type="text" name="k1" value="';
			printf("%s", htmlentities($k1));
			echo '" />';
			echo '			  <input class="postshort" type="text" name="k2" value="';
			printf("%s", htmlentities($k2));
			echo '" />';
			echo '			  <input class="postshort" type="text" name="k3" value="';
			printf("%s", htmlentities($k3));
			echo '" />';
			echo '			  <input class="postshort" type="text" name="k4" value="';
			printf("%s", htmlentities($k4));
			echo '" />';
			echo '			  <input class="postshort" type="text" name="k5" value="';
			printf("%s", htmlentities($k5));
			echo '" /><br />';
			
			
			if(!empty($image_db)){
				echo '<br />Remove image?<br /><select class="login" name="delimage">
								<option value="1">No</option>
								<option value="2">Yes</option>
							</select><br />';
			}
			echo '		      <br /><input class="goto" name="uploadedfile" type="file" accept="image/*" id="uploadfile_input" /><br /><br />';
			echo '<input class="goto" type="submit" value="SUBMIT" name="submit" />
						</form>
						<p>Fun trick: Type ^br^ if you want to insert a new line.</p>
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
			$getstory->close();
			$mysqli->close();
			header("Location: mod3index.php");
			exit;
		}
	}
	else {
		$getstory->close();
		$mysqli->close();
		header("Location: mod3index.php");
		exit;
	}

	$getstory->close();
	$mysqli->close();
}
else {
    header("Location: mod3index.php");
    exit;
}



?>
</body>
</html>
