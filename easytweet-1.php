   <div id="container">  	   
   	   
      	   <form method='get' action='easytweet (1).php'>
			   <input type="radio" name="searchVal" value = "5"> 5<br>
			   <input type="radio" name="searchVal" value = "10"> 10<br>
			   <input type="radio" name="searchVal" value = "15"> 15<br>
			   <input type="radio" name="searchVal" value = "20"> 20<br>
      	       <input type='text' name='searchTerm' value='' size='15'>
      	       <input type='submit' name='action' value='Search'>
      	   </form> 
      </div>
      <div id="content">
<?php
require ("TwitterAPI.php");

function printJSON($jsonData) {
	$jsonString = htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT));
	echo "<pre>" . $jsonString . "</pre>";
}

function showTweetProperties($tweet) {
            	
	if (sizeof($tweet->entities->urls) > 0) {
		echo "<p>URLS<br>";
		foreach ($tweet->entities->urls as $url) {
			echo "<a href='$url->url'>$url->url</a><br>";
		}
		echo "</p>";
	}			
	if (sizeof($tweet->entities->hashtags) > 0) {
		echo "<p>HASHTAGS<br>";
		foreach ($tweet->entities->hashtags as $hashtag) {
			$searchTerm = $hashtag->text;
			echo "<a href='easytweet (1).php?action=hashtag&searchTerm=$searchTerm'>#$searchTerm</a><br>";
		}
		echo "</p>";
	}
	
	if (sizeof($tweet->entities->user_mentions) > 0) {
		echo "<p>USER MENTIONS<br>";
		foreach ($tweet->entities->user_mentions as $user_mention) {
			$searchTerm = $user_mention->screen_name;
			echo "<a href='easytweet (1).php?action=user&searchTerm=$searchTerm'>@$searchTerm</a><br>";
		}
		echo "</p>";
	}	
	if (property_exists($tweet->entities, 'media')) {
		foreach ($tweet->entities->media as $media) {
		?>	    
			<img src ="<?php echo $media->media_url; ?>" /><br> <?php
		}
		echo "</p>";
	}	
}//function showtweetproperties
//----------------------------------------------------
// MAIN BODY - get action and make call to Twitter API
//---------------------------------------------------- 
		$twitterURL = 'search/tweets.json';
		$params = array('q' => $searchTerm,
						'count' => $searchVal);				
		$twitterData = callTwitter($twitterURL, $params);
		$tweets = $twitterData->statuses;
// un-comment the next line to see the structure of the twitter data
//printJSON($tweets);

	
// display tweets on web page

foreach ($tweets as $tweet) {

    /* Display the screen_name property as a clickable hyperlink to load that user's timeline (1d)
       Note - this is NOT a link to twitter.com, but a link to your easytweet (1) application */    
	echo "<hr><p>Tweet from @";
	$username = $tweet->user->screen_name;
$url = "easyTweet (1).php?action=user&searchTerm=$username";
echo "<a href='$url'>$username</a>";
	
	//time and text
	echo $tweet->created_at. "<br>";
	echo $tweet->text."</p>";
	showTweetProperties($tweet);
}
?>
      </div> <!-- end of content div -->
   </div> <!-- end of container div -->
