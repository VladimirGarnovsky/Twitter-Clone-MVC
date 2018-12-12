<?php
// will root the funtion to the neccessarry actions
	include("functions.php");

	if($_GET['action'] == "loginSignup")
    {
      $error="";
      if(!$_POST['email']) 
      {	
          $error = "An email address is required";
      }
      else if(!$_POST['password']) 
      {	
          $error = "A password is required";
      }
      else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $error = "Please enter a valid email";
      }

      if($error!=""){
        echo $error;
        exit();
      }      
      if($_POST['loginActive'] == "0") // signup
      {
        $query = "SELECT * FROM users WHERE email= '".mysqli_real_escape_string($link,$_POST['email'])."' limit 1";
        $result = mysqli_query($link,$query);
        if(mysqli_num_rows($result) != 0) $error = "The email you are trying to use is taken";
        else 
        {
         // create user
         $query = "INSERT INTO users (email,password) VALUES ('".mysqli_real_escape_string($link,$_POST['email'])."','".mysqli_real_escape_string($link,$_POST['password'])."')";
          
          if(mysqli_query($link,$query))
          {
            $_SESSION['id'] = mysqli_insert_id($link);
          	echo 1;
            $query = "UPDATE users SET password = '". md5(md5( $_SESSION['id']).$_POST['password']) ."' WHERE id=". $_SESSION['id']." LIMIT 1";
          	mysqli_query($link,$query);
            
          }
          else 
          {
          	$error = "Couldn't create user, please try again later:";
          }
        }
      }
      else
      {
      // log in
                $query = "SELECT * FROM users WHERE email= '".mysqli_real_escape_string($link,$_POST['email'])."' limit 1";
        		$result = mysqli_query($link,$query);
        		$row = mysqli_fetch_assoc($result);
        		if($row['password'] == md5(md5($row['id']).$_POST['password'] )) {
                  echo 1;
                  $_SESSION['id'] = $row['id'];
                }
                else
                  $error = "could not find username/password combination\n"; 
      
      }
    }
	if($error!=""){
     echo $error;
     exit();
    }
	if($_GET['action']=='toggleFollow'){
                $query = "SELECT * FROM isFollowing WHERE follower= ".mysqli_real_escape_string($link,$_SESSION['id'])." AND isFollowing=".mysqli_real_escape_string($link,$_POST['userId'])." limit 1";
      			//echo $query;
        		$result = mysqli_query($link,$query);
      			if(mysqli_num_rows($result) > 0) { // are following
                	$row = mysqli_fetch_assoc($result);
                  	mysqli_query($link,"DELETE FROM isFollowing WHERE id=".mysqli_real_escape_string($link,$row['id'])." LIMIT 1");
                  	echo "1"; // unfollowed
                }
      			else 
                {
                    $query = "INSERT INTO isFollowing  (follower,isFollowing) VALUES (".mysqli_real_escape_string($link,$_SESSION['id']).",".mysqli_real_escape_string($link,$_POST['userId']).")"; 
                  	mysqli_query($link,$query);
                  	echo "2"; // follower
                }
    }
    if($_GET['action'] == 'postTweet')
    {
      		if(!$_POST['tweetContent'])
            {
              echo 'your tweet is empty';
            }
      		else if(strlen($_POST['tweetContent']) > 140)
            {
              echo 'your tweet is too long';
            }
      		else
            {
              $query = "INSERT INTO tweets  (tweet,userid,datetime) VALUES('".mysqli_real_escape_string($link,$_POST['tweetContent'])."',".mysqli_real_escape_string($link,$_SESSION['id']).",NOW())"; 
              mysqli_query($link,$query);
              echo 1;
            }
    }
?>