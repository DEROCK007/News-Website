<!DOCTYPE html>
<html lang="en">
<head>
    <title>Post New Story</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body class="common">


<?php
session_start();
date_default_timezone_set('America/Chicago');
//load username
$username = (string) @$_SESSION['username'];

if(!empty($username)){
	
	echo '  <div class="headtitle">&nbsp;&nbsp; Post New Story</div>
  <div class="headstrip"></div>
  <div class="bodyblock">
    <div class="bodybox">
      
      
      <br />
      
      <form enctype="multipart/form-data" action="mod3posting.php" method="POST">

        <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
	echo '		<input type="hidden" name="token" value="';echo $_SESSION['token'];echo '" />';
	echo '<input class="posttitle" type="text" name="title" placeholder="Title..." />
        
        <br />
        
        <textarea class="posttext" name="story" placeholder="Story..."></textarea>
        
        <br />
        <br />
      
        <input class="postattr" type="text" name="link" placeholder="Link..." />
        
        <br />
        <br />
      
        <input class="postshort" type="text" name="k1" placeholder="keyword1" />

        <input class="postshort" type="text" name="k2" placeholder="keyword2" />

        <input class="postshort" type="text" name="k3" placeholder="keyword3" />

        <input class="postshort" type="text" name="k4" placeholder="keyword4" />

        <input class="postshort" type="text" name="k5" placeholder="keyword5" />

        <br />
        <br />
      
        <input class="goto" name="uploadedfile" type="file" accept="image/*" id="uploadfile_input" />
      
        <br />
        <br />
      
        <input class="goto" type="submit" value="SUBMIT" name="submit" />

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
else {
	header("Location: mod3index.php");
	exit;
}

?>
</body>
</html>
