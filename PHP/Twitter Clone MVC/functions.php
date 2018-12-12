<?php
	session_start();
	$link = mysqli_connect("*****","*********","********","*********");
	if(mysqli_connect_errno()){
      	print_r(mysqli_connect_errno());
      	exit();
    }
	if($_GET['function'] == "logout") {
    	session_unset();
    }



	function time_since($since) {
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'h'),
            array(60 , 'min'),
            array(1 , 's')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
        return $print;
    }


	function displayTweets($type) {
      	global $link;
      	if ($type == 'public') {
        	
          $whereClause = "";
        
        } 
      	else if($type == 'isFollowing')
        {
                     $query = "SELECT * FROM isFollowing WHERE follower= ".mysqli_real_escape_string($link,$_SESSION['id']);
                    $result = mysqli_query($link,$query);
          			$whereClause = "";
          
					while($row = mysqli_fetch_assoc($result))
                    {
                      	if($whereClause == "") $whereClause = "WHERE "; 
                      	else $whereClause .= "OR ";
                      	$whereClause.= " userid=".$row['isFollowing'];
                    }

         
        }
        else if($type == 'yourtweets') {
         	 $whereClause = "WHERE userid=".mysqli_real_escape_string($link,$_SESSION['id']);
        }
        else if($type == 'search') {
          	 echo '<p>Showing search results for "'.mysqli_real_escape_string($link, $_GET['q']).'":</p>';
         	 $whereClause = "WHERE tweet LIKE '%".mysqli_real_escape_string($link,$_GET['q'])."%'";
        }
        else if(is_numeric($type))
        {
    		$whereClause = "WHERE userid=".mysqli_real_escape_string($link, $type);
        }
      $query = "SELECT * FROM tweets ". $whereClause . " ORDER BY 'datetime' DESC LIMIT 10";

      $result = mysqli_query($link,$query);
      
      if(mysqli_num_rows($result) == 0) {

       		echo 'The are no tweets to display'; 
      } 

      else
      {
        	while($row = mysqli_fetch_assoc($result)) {
              $userQuery = "SELECT * FROM users WHERE id = ". mysqli_real_escape_string($link, $row['userid']) . " LIMIT 1";
              $userQueryResult = mysqli_query($link,$userQuery);
              $user = mysqli_fetch_assoc($userQueryResult);
			   
              echo '<div class="tweet"> <p>'. $user['email'] . ' <span class="time">'.time_since(time() - strtotime($row['datetime'])  ). '</span>:</p>';
              echo '<p>'.$row['tweet'].'</p>';
              echo "<p> <a class='toggleFollow' data-userId='".$row['userid']."'>";
              

                              $isFollowingQuery = "SELECT * FROM isFollowing WHERE follower= ".mysqli_real_escape_string($link,$_SESSION['id'])." AND      isFollowing=".mysqli_real_escape_string($link,$row['userid'])." limit 1";
      			
        		$isFollowingQueryResult = mysqli_query($link,$isFollowingQuery);
      			if(mysqli_num_rows($isFollowingQueryResult) > 0)
                { // are following
                	
				echo 'Unfollow';
                }
              	else
                {
                  echo 'Follow';
                }
              
              
              //
              echo "</a></p></div>";
            }
      }
    }
	function displaySearch() {
      	echo '
        <form class="form-inline">
          <div class="form-group">
            <input type="hidden" name="page" value="search">
            <input type="text" name="q" class="form-control mb-2 mr-sm-2" id="search" placeholder="Search Tweets">
          </div>

          <button type="submit" class="btn btn-primary mb-2">Search</button>
        </form>
        ';
    }
	function displayTweetBox() {
    	if($_SESSION['id']>0){
          	echo '
            
          		  <div id="tweetSuccess" class="alert alert-success">Your tweet was posted successfully.</div>
           	      <div id="tweetFail" class="alert alert-danger"></div>
                  <div class="form">
                    <div class="form-group">
                      <textarea class="form-control mb-2 mr-sm-2" id="tweetContent" ></textarea>
                    </div>

                    <button class="btn btn-primary mb-2" id="postTweetButton">Post Tweet</button>
                  </div>

            
            
            ';
        }
    }
	function displayUsers(){
      
      global $link;
      $query = "SELECT * FROM users ". $whereClause . " ORDER BY 'datetime' DESC LIMIT 10";
      $result = mysqli_query($link,$query);
    
      while($row = mysqli_fetch_assoc($result)) {
    		echo '<a href="?page=publicprofiles&userid='.$row['id'].'">'.$row['email'].'</a><br>';	
      }
    }
?>