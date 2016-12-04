<?php

session_start();
date_default_timezone_set('America/Chicago');

//load username
$username = (string) @$_SESSION['username'];

if(!empty($username)){
	
	if($_POST['submit'] == "SUBMIT") {
		
		require 'databaselogin.php';
		
		//read post form
		$title = (string) $_POST['title'];		//no need to escape since using prepare
		$k1 = (string) $_POST['k1'];
		$k2 = (string) $_POST['k2'];
		$k3 = (string) $_POST['k3'];
		$k4 = (string) $_POST['k4'];
		$k5 = (string) $_POST['k5'];
		$link = (string) $_POST['link'];
		$story = (string) $_POST['story'];
		$datetime = date('m/d/Y H:i:s');
		$imagepath = NULL;
		
		//verify token
		if($_SESSION['token'] !== $_POST['token']){
			die("Request forgery detected");
		}
		
		//upload if set
		$filename = basename($_FILES['uploadedfile']['name']);
		if(!empty($filename)){
			
			if( !preg_match('/^[\w_\.\-]+$/', $filename) ){			//throw irregular filename (space, special chars and stuff...)
				echo "Invalid filename";
				exit;
			}
			$userdir    = 'userdata/'.$username;
			$filename = date('mdY_His').'_'.$filename;
			$full_path = $userdir.'/'.$filename;
			if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){
				$imagepath = (string) $full_path;
			}
			else{
				echo "Move File Error";
			}
		}
		else{
			$imagepath = NULL;
		}
		
		$username_q = $mysqli->real_escape_string($username);
		$putstory = $mysqli->prepare("insert into story (user_id, title, link, text, keyword1, keyword2, keyword3, keyword4, keyword5, timestamp_1, image) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		if(!$putstory){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$putstory->bind_param('sssssssssss', $username_q, $title, $link, $story, $k1, $k2, $k3, $k4, $k5, $datetime, $imagepath);
		$putstory->execute();
		$putstory->close();
		
		//check current st_id
		$checkcurr = $mysqli->prepare("select st_id from story where user_id=? order by st_id DESC");
		if(!$checkcurr){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$checkcurr->bind_param('s', $username_q);
		$checkcurr->execute();
		$checkcurr->bind_result($curr_st_id);
		$checkcurr->fetch();
		$checkcurr->close();
		
		$mysqli->close();
		
		if(!empty($curr_st_id)) {
			$addr = "Location: mod3content.php?st_id=".$curr_st_id;
			header($addr);
			exit;
		}
		else {
			printf("Posting Failed");
			exit;
		}
	}
	else {
		header("Location: mod3index.php");
		exit;
	}
}
else{
	header("Location: mod3index.php");
	exit;
}
?>
