<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Story</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">
<?php
session_start();

//load username
$username = (string) @$_SESSION['username'];
$_SESSION['del_st_id'] = NULL;

if(!empty($username)){
    require 'databaselogin.php';
    
    //get st_id to edit
    $st_id = (string) @$_GET['del_st_id'];
    
    if(!empty($st_id)){         
        
        //fetch target story info
        $gettarget = $mysqli->prepare("select user_id, title from story where st_id=?");
        if(!$gettarget){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $gettarget->bind_param('i', $st_id);
        $gettarget->execute();
        $gettarget->bind_result($userchk_db, $title_db);
        if($gettarget->fetch()){
            $title = $title_db;     //are these necessary?
            $userchk = $userchk_db;
        }
        else{                       //no story of st_id found
            $gettarget->close();
			$mysqli->close();
			header("Location: mod3index.php");
			exit;
        }
        $gettarget->close();
        
        //check author
        if($username == $userchk){
            echo '  <div class="headtitle">&nbsp;&nbsp; COMFIRM DELETE</div>
  <div class="headstrip"></div>
  <div class="bodyblock">
    <div class="bodybox">
      <div class="notice">
	  Are you sure you want to delete';       //a self submiting form for delete confirmation
                printf(" %s " , htmlentities($title));
            echo '? <br/><br/>
              <form method="POST">
                <input class="goto" type="submit" value="Confirm Delete" name="deleteok" />';
            echo '		<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
            echo '
                <input class="goto" type="submit" value="Cancel Delete" name="back" />
              </form>
			   </div>
				</div>
			
				<br/><br/><br/><br/>
			  </div>';
            $_SESSION['del_st_id']=$st_id;
            
            if(@$_POST['deleteok'] == "Confirm Delete"){          //if delete confirmed, we delete
                if(!empty($_SESSION['del_st_id'])){
                    $confirmdel=$_SESSION['del_st_id'];
                    //verify token
                    if($_SESSION['token'] !== $_POST['token']){
                        die("Request forgery detected");
                    }
                    
					$deltarget = $mysqli->prepare("DELETE FROM comment WHERE sty_id=?");
                    if(!$deltarget){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $deltarget->bind_param('i', $confirmdel);
                    $deltarget->execute();
                    $deltarget->close();
					
                    $deltarget = $mysqli->prepare("DELETE FROM story WHERE st_id=?");
                    if(!$deltarget){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $deltarget->bind_param('i', $confirmdel);
                    $deltarget->execute();
                    $deltarget->close();
                }
                $_SESSION['del_st_id'] = NULL;
                $mysqli->close(); 
                header("Location: mod3userindex.php");
                exit;
            }
            elseif (isset($_POST['back'])) {            //i dont really think a back button needs token
                $_SESSION['del_st_id'] = NULL;
                $mysqli->close(); 
                header("Location: mod3userindex.php");
                exit;
            }
            $_SESSION['del_st_id'] = NULL;
        }
        else {          //not author
            $mysqli->close(); 
            header("Location: mod3index.php");
            exit;
        }
    }
    else {      //no st_id parameter
        $mysqli->close(); 
        header("Location: mod3index.php");
        exit;
    }
}
else {
    header("Location: mod3index.php");
	exit;
}
