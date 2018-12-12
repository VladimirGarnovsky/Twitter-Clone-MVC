<div class="container mainContent">
	<div class="row">
      <div class="col-sm-8"><h2>Tweets for you</h2>
      		<?php displayTweets('isFollowing'); ?>
        
      </div>
      <div class="col-sm-4">
        <?php displaySearch(); ?>
        <hr>
      	<?php displayTweetBox(); ?>
      </div>
    </div>
</div>