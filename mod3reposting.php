<?php

session_start();
date_default_timezone_set('America/Chicago');

//load username
$username = (string) @$_SESSION['username'];

if(!empty($username)){

    if($_POST['submit'] == "SUBMIT"){
        require 'databaselogin.php';
        
        //get st_id to edit
        $st_id = (string) @$_SESSION['edit_st_id'];
        $_SESSION['edit_st_id'] = NULL;     //nullify session var
        
        if(!empty($st_id)){
            $title = (string) $_POST['title'];       //no need to escape since using prepare
            $k1 = (string) $_POST['k1'];
            $k2 = (string) $_POST['k2'];
            $k3 = (string) $_POST['k3'];
            $k4 = (string) $_POST['k4'];
            $k5 = (string) $_POST['k5'];
            $link = (string) $_POST['link'];
            $story = (string) $_POST['story'];
            $imagepath = NULL;
            
            //verify token
            if($_SESSION['token'] !== $_POST['token']){
                die("Request forgery detected");
            }
            
            //fetch original image path
            $getimagepath = $mysqli->prepare("select user_id, image from story where st_id=?");
            if(!$getimagepath){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $getimagepath->bind_param('i', $st_id);
            $getimagepath->execute();
            $getimagepath->bind_result($userchk_db, $image_db);
            if($getimagepath->fetch()){
                $imagepath = $image_db;     //are these necessary?
                $userchk = $userchk_db;
            }
            else{
                $imagepath = NULL;
            }
            $getimagepath->close();
                
            //author check
            if($username == $userchk) {
                //update if upload set
                $filename = basename($_FILES['uploadedfile']['name']);
                
                if(@$_POST['delimage'] == 2){
                    //delete old file
                    if(!empty($imagepath)){
                        $deletecheck = unlink($imagepath);
                        if(!$deletecheck){
                            echo "Delete File Error";
                        }
                    }
                    $imagepath = NULL;
                    $filename = NULL;
                }
                
                if(!empty($filename)){
                    
                    if( !preg_match('/^[\w_\.\-]+$/', $filename) ){			//throw irregular filename (space, special chars and stuff...)
                        echo "Invalid filename";
                        exit;
                    }
                    $userdir = 'userdata/'.$username;
                    $filename = date('mdY_His').'_'.$filename;
                    $full_path = $userdir.'/'.$filename;
                    if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){			//if upload(move) success, goto index
                        
                        //delete old file
                        if(!empty($imagepath)){
                            $deletecheck = unlink($imagepath);
                            if(!$deletecheck){
                                echo "Delete File Error";
                            }
                        }
                        
                        //update new path
                        $imagepath = (string) $full_path;        //no need to escape
                    }
                    else{
                        echo "Move File Error";
                    }
                }
    
                $updatestory = $mysqli->prepare("UPDATE story SET title=?, link=?, text=?, keyword1=?, keyword2=?, keyword3=?, keyword4=?, keyword5=?, image=? WHERE st_id=?");
                if(!$updatestory){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $updatestory->bind_param('ssssssssss', $title, $link, $story, $k1, $k2, $k3, $k4, $k5, $imagepath, $st_id);
                $updatestory->execute();
                $updatestory->close();
                
                $mysqli->close();
                
                $addr = "Location: mod3content.php?st_id=".$st_id;
                header($addr);
                exit;
            }
            else {          //not author
                $mysqli->close();
                header("Location: mod3index.php");
                exit;
            }
        }
        else {          //empty st_id parameter
            $mysqli->close();
            header("Location: mod3index.php");
            exit;
        }
    }
    else {          //not sent by submit button
        header("Location: mod3index.php");
        exit;
    }
}
else {          //not logged in
    header("Location: mod3index.php");
	exit;
}
?>
