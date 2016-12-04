<?php

session_start();
date_default_timezone_set('America/Chicago');

//load username
$username = (string) @$_SESSION['username'];

if(!empty($username)){
    if($_POST['submit'] == "SUBMIT"){
        require 'databaselogin.php';
        
        //get com_id to edit
        $com_id = (string) @$_SESSION['edit_com_id'];
        $_SESSION['edit_com_id'] = NULL;     //nullify session var
        
        if(!empty($com_id)){
            $com = (string) $_POST['com'];       //no need to escape since using prepare
            
            //verify token
            if($_SESSION['token'] !== $_POST['token']){
                die("Request forgery detected");
            }
            
            //fetch original comment info
            $gettargetcom = $mysqli->prepare("select usr_id, sty_id from comment where com_id=?");
            if(!$gettargetcom){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $gettargetcom->bind_param('i', $com_id);
            $gettargetcom->execute();
            $gettargetcom->bind_result($comuserchk_db, $sty_id_db);
            if($gettargetcom->fetch()){
                $userchk = $comuserchk_db;
                $sty_id = $sty_id_db;
            }
            else{
                $gettargetcom->close();
                $mysqli->close();
                header("Location: mod3index.php");
                exit;
            }
            $gettargetcom->close();
                
            //author check
            if($username == $userchk) {
                $updatecomment = $mysqli->prepare("UPDATE comment SET text=? WHERE com_id=?");
                if(!$updatecomment){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $updatecomment->bind_param('ss', $com, $com_id);
                $updatecomment->execute();
                $updatecomment->close();
                
                $mysqli->close();
                
                $addr = "Location: mod3content.php?st_id=".$sty_id;
                header($addr);
                exit;
            }
            else {          //not author
                $mysqli->close();
                header("Location: mod3index.php");
                exit;
            }
        }
        else {          //empty com_id parameter
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
