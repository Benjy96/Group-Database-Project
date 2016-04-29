<?php
session_start();
include ("dbConnect.php");

if(isset($_SESSION["currentUser"])){
	$userID = $_SESSION["currentUser"];
	$currentUserID = $_SESSION["currentUserID"];
}
//checks if ?logout has been passed via URL
if (isset($_GET["logout"])) {
	
	unset($_SESSION["currentUser"]);
	unset($_SESSION["currentUserID"]);
	unset($_POST["action"]);
}

//if current user isn't set, unset ID (just safety)
if (!isset($_SESSION["currentUser"])){
	
	unset($_SESSION["currentUser"]);
	unset($_SESSION["currentUserID"]);
	header("Location: main.php");
}

//if signup form has been submitted, check user/password
if (isset($_POST["action"]) && $_POST["action"]=="signup") {

	$signupUser=$_POST["sUsername"];
	$signupPass=$_POST["sPassword"];
	
	include ("dbConnect.php");
	
	$dbQuery2=$db->prepare("select * from users
							where username=:signupUser");
	$dbParams2 = array('signupUser'=>$signupUser);
	$dbQuery2->execute($dbParams2);
	$dbRow2=$dbQuery2->fetch(PDO::FETCH_ASSOC);
	
	if($dbRow2["username"]==$signupUser){
		
		echo"<script>alert('Account name taken');</script>";
	}else{
	
	$dbQuery=$db->prepare("insert into users 
							values (null, :sUsername, :sPassword)"); 
	$dbParams = array('sUsername'=>$signupUser, 'sPassword'=>$signupPass);
	$dbQuery->execute($dbParams);
	
	echo"<script>alert('Account Created!');</script>";
	
	$_SESSION["currentUser"]=$signupUser;
	
	$dbQuery2=$db->prepare("select * from users
							where username=:signupUser");
	$dbParams2 = array('signupUser'=>$signupUser);
	$dbQuery2->execute($dbParams2);
	$dbRow2=$dbQuery2->fetch(PDO::FETCH_ASSOC);
	
	$_SESSION["currentUserID"]=$dbRow2["id"];
		}	
	}
//if login form has been submitted, check user/password
if (isset($_POST["action"]) && $_POST["action"]=="login") {

	//set form values to PHP variables for validation
	$formUser=$_POST["username"];
	$formPass=$_POST["password"];

	include ("dbConnect.php");

	//check users in database
	$dbQuery=$db->prepare("select * from users where username=:formUser"); 
	$dbParams = array('formUser'=>$formUser);
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
      if ($dbRow["username"]==$formUser) {       
         if ($dbRow["password"]==$formPass) {
            $_SESSION["currentUser"]=$formUser;
            $_SESSION["currentUserID"]=$dbRow["id"];
			header("Location: gamestore.php");
				/*if (isset($_SESSION["tracklist"])) 
                 header("Location: addToBasket.php");
            else header("Location: shopForTracks.php");  		*/	
         }//password if
         else {
            header("Location: gamestore.php?failCode=2");
         }
      }//user if 
	  else {
            header("Location: gamestore.php?failCode=1");
      }

   } else {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<!-- sets the width of the page to follow the screen-width of the device (which will vary depending on the device). -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="bootstrap/boot/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="bootstrap/boot/js/jquery-2.2.3.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="bootstrap/boot/js/bootstrap.min.js"></script>
<!-- custom stylesheet -->
<link rel="stylesheet" href="gamestore_css.css">
</head>
<body>

<!-- navbar --> 
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid" id = "header">
    <div class="navbar-header">
      <a class="navbar-brand" href="#" id="menu-toggle"><span class="glyphicon glyphicon-arrow-up" id="menu-arrow"></span> Bean Industries</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="main.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
      <li><a href="gamestore.php"><span class="glyphicon glyphicon-off"></span> Game Store</a></li>
      <li><a href="#"><span class="glyphicon glyphicon-earphone"></span> Contact Us</a></li>
    </ul>
	
	<?php
	if (isset($_SESSION["currentUser"])) { 
	
	$userID = $_SESSION["currentUser"];
	$currentUserID = $_SESSION["currentUserID"];
	
    $dbQuery=$db->prepare("select * from basket where paid='N' and userID=:currentUserID");
    $dbParams = array('currentUserID'=>$_SESSION["currentUserID"]);
    $dbQuery->execute($dbParams);
    $numGames=$dbQuery->rowCount();
	}
	
	if(!isset($_SESSION["currentUser"])){
	// right side of navbar-
	echo "<ul class=\"nav navbar-nav navbar-right\">";
		echo "<li><a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\"><span class=\"glyphicon glyphicon-user\"></span> Sign Up </a></li>";
		echo "<li><a href=\"#\" data-toggle=\"modal\" data-target=\"#myModalLogin\"><span class=\"glyphicon glyphicon-log-in\"></span> Login </a></li>";
	echo "</ul>";
	} else {
		echo "<ul class=\"nav navbar-nav navbar-right\">";
			echo "<li><a href=\"#\" data-toggle=\"modal\" data-target=\"#myUserModal\"><span class=\"glyphicon glyphicon-user badge\"> ".$numGames."</span> ". $_SESSION["currentUser"] ."</a></li>";
			echo "<li><a href=\"#\"><span class=\"glyphicon glyphicon-gbp\"></span> Your Orders </a></li>";
		echo "</ul>";
		
	}
	?>
  </div><!-- fluid container -->  
</nav><!-- NAV BAR TOP OF PAGE -->


<!-- Modal user options -->
<div id="myUserModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User Information</h4>
      </div>
	  <!-- MODAL BODY -->
      <div class="modal-body">
		<ul class="list-group">			
			<li class="list-group-item list-group-item-danger">You have <?php echo "$numGames games in your basket"?></li>
		</ul>
		<!-- LOGOUT NEEDED -->
		&nbsp;&nbsp;<a href="gamestore.php?logout">Log out</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="main.php">Basket</a>	
	</div><!-- body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Sign Up -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sign Up</h4>
      </div>
	  <!-- MODAL BODY -->
      <div class="modal-body">
		<form role="form" name="signup" method = "post" action="gamestore.php">
			<div class="form-group">
				<label for="usr">Username:</label>
				<input type="text" class="form-control" name="sUsername" placeholder="Enter Name...">
			</div>
			
			<div class="form-group">
				<label for="pwd">Password:</label>
				<input type="password" class="form-control" name="sPassword"placeholder="Enter Password...">
			</div>
			<!-- Submit button -->
			<input type="hidden" name="action" value="signup">
		<button type="submit" class="btn btn-default" value="Signup">Submit</button>
	</form>   
  </div>
  
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Login -->
<div id="myModalLogin" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Log In</h4>
      </div>
	  <!-- MODAL BODY -->
      <div class="modal-body">
        <form role="form" name="login" method ="post" action="gamestore.php">
			<div class="form-group">
				<label for="usr">Username:</label>
				<input type="text" name="username" class="form-control" placeholder= "Enter Name...">
			</div>
			
			<div class="form-group">
				<label for="pwd">Password:</label>
				<input type="password" name="password" class="form-control" placeholder="Enter Password...">
			</div>
			<?php 
			if (isset($_GET["failCode"])) {
				if ($_GET["failCode"]==1)
					echo "<p class=\"bg-danger\">Wrong username detected.</p>";
				if ($_GET["failCode"]==2)
					echo "<p class=\"bg-danger\">Wrong password detected.</p>";
			}//if  
			?>
			<!-- Submit button -->
			<input type="hidden" name="action" value="login">
		<button type="submit" class="btn btn-default" value="Login">Submit</button>
	</form> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Main Wrapper -->
<div id = "wrapper">

<!-- Sidebar -->
<br>
<div id="sidebar-wrapper">
<br>
<br>
	<div id = "twitterapi"><!-- TWITTER -->
	  <div id="twitter-container">
     	   <form method='get' action='gamestore.php'>
      	       <!-- Add a set of radio buttons for the user to choose how many results to return -->
      	       <input type='text' name='searchTerm' value='' size='15'>
      	       <input type='submit' name='action' value='Search'>
      	   </form>	   
      </div>
<?php
require ("TwitterAPI.php");

function printJSON($jsonData) {
	$jsonString = htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT));
	echo "<pre>" . $jsonString . "</pre>";
}
function showTweetProperties($tweet) {

    /* Modify the 'if' statements in this block so that the headings are only displayed if 
       there is any content to show (1c) */
            	
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
			echo "<a href='easytweet-1 (1).php?action=hashtag&searchTerm=$searchTerm'>#$searchTerm</a><br>";
		}
		echo "</p>";
	}
	
	if (sizeof($tweet->entities->user_mentions) > 0) {
		echo "<p>USER MENTIONS<br>";
		foreach ($tweet->entities->user_mentions as $user_mention) {
			$searchTerm = $user_mention->screen_name;
			echo "<a href='easytweet-1 (1).php?action=user&searchTerm=$searchTerm'>@$searchTerm</a><br>";
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
if (isset($_GET['action'])) {

	if ($_GET['action']=='hashtag') {
		$hashtag=trim($_GET['searchTerm']);
		$twitterURL = 'search/tweets.json';
		$params = array('q' => $hashtag,
    	                'count' => '10');
    	$twitterData = callTwitter($twitterURL, $params);
    	$tweets = $twitterData->statuses;	                
	}

	if ($_GET['action']=='user') {
		$user=trim($_GET['searchTerm']);
		$twitterURL = 'statuses/user_timeline.json';
		$params = array('screen_name' => $user,
    	                'count' => '10');               
    	$tweets = callTwitter($twitterURL, $params);	                
	}	
	
	if ($_GET['action']=='Search') {
		$searchTerm=trim($_GET['searchTerm']);
		$searchVal="40";
		$twitterURL = 'search/tweets.json';
		$params = array('q' => $searchTerm,
						'count' => $searchVal);				
		$twitterData = callTwitter($twitterURL, $params);
		$tweets = $twitterData->statuses;
		/* Replace the following line with code that accepts the search term
		   provided by the user (the $searchTerm variable) and returns the selected number 
		   of matching tweets (2b) */ 
    	//$tweets = callTwitter($twitterURL, $params);                
	}
	
} elseif(isset($_GET["gameno"])) {  // no action specified so show home timeline

//if game has been selected, go to information page
	$gameNum3 = $_GET["gameno"];
		
	//QUERY FOR IMAGE AND GAMES
	$dbQuery3=$db->prepare("select id, title from gamelist");       
	$dbQuery3->execute();
	$numTracks3=$dbQuery3->rowCount();
	
	//while loop breaks once it reaches correct game
	while ($dbRow3=$dbQuery3->fetch(PDO::FETCH_NUM)){
		if($dbRow3[0] == $gameNum3)
			break;
	}

		$searchVal="40";
		$searchTerm=$dbRow3[1];
		
		$twitterURL = 'search/tweets.json';
		$params = array('q' => $searchTerm,
						'count' => $searchVal);				
		$twitterData = callTwitter($twitterURL, $params);
		$tweets = $twitterData->statuses;

}
else{
		$searchVal="40";
		$searchTerm="gaming";
		
		$twitterURL = 'search/tweets.json';
		$params = array('q' => $searchTerm,
						'count' => $searchVal);				
		$twitterData = callTwitter($twitterURL, $params);
		$tweets = $twitterData->statuses;	
}
// un-comment the next line to see the structure of the twitter data
//printJSON($tweets);
// display tweets on web page

foreach ($tweets as $tweet) {

    /* Display the screen_name property as a clickable hyperlink to load that user's timeline (1d)
       Note - this is NOT a link to twitter.com, but a link to your easytweet-1 (1) application */    
	echo "<hr><p>Tweet from @";
	$username = $tweet->user->screen_name;
$url = "easytweet-1 (1).php?action=user&searchTerm=$username";
			echo "<a href='$url'>$username</a>";
	
	//time and text
	echo $tweet->created_at. "<br>";
	echo $tweet->text."</p>";
	showTweetProperties($tweet);
}

?>
      </div> <!-- end of content div -->
   </div> <!-- end of container div -->
    <?php 
	if (!isset($_SESSION["currentUserID"])) 
     header("Location: main.php");
	
	if (isset($_POST["action"]) && $_POST["action"]=="deleteFromBasket") {
     $dbQuery=$db->prepare("delete from basket where id=:id");
     $dbParams = array('id'=>$_POST["purchaseID"]);
     $dbQuery->execute($dbParams);
  }
   
   $dbQuery=$db->prepare("select gamelist.title, gamelist.ageRating, gamelist.price, basket.id ".
                      "from basket, gamelist ".
                      "where basket.userID=:userID ".
                      "and basket.paid='N' ".
                      "and basket.gameID=gamelist.id ");
                      
	$dbParams = array('userID'=>$_SESSION["currentUserID"]);                      
	$dbQuery->execute($dbParams);
	$numGames=$dbQuery->rowCount();

	if ($numGames==0)
	   echo "<h3>Your basket is empty</h3>";
	else { ?>
   
   <!-- Page Content -->
   <div class="row">
   <br>
	<br>
	<br>
	<br>
	<br>
	<br>
   <div class="container">
   <h2>Basket</h2>
   <table class="table">
   <thead>
	<tr>
	<th>Title</th>
	<th>Age Rating</th>
	<th>Price</th>
	<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
   <?php 
	$priceCounter = 0;
	$rowCounter = 0;
	
   while ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td>$dbRow[0]</td><td>$dbRow[1]</td><td>&pound;$dbRow[2]</td>".
			"<td class=\"noborder\">".
           "<form class=\"inline\" method=\"post\" action=\"purchaseGames.php\">".
           "<input type=\"hidden\" name=\"action\" value=\"deleteFromBasket\">".
           "<input type=\"hidden\" name=\"purchaseID\" value=\"$dbRow[3]\">".
           "<input type=\"image\" src=\"delete.png\">".
           "</form></td></tr>"; 
	   $priceCounter+=$dbRow[2];
	   $rowCounter++;
   } 
   echo "</tbody>";
   echo "</table>";
   echo "</div>";
   
   echo "<div class=\"well\" id=\"price-well\">";
   $totalSum = $priceCounter; 
		
		//if more than 5 tracks apply a 15% discount to total price
		if($rowCounter > 5){
			$discount = 0.15 * $totalSum;
			$totalSum = $totalSum - $discount;
			echo "<h2>Your Discounted Price Total Comes To: ".$totalSum."<h2>";
		}else{
			echo "<h2>Your Total Price Comes To: ".$totalSum."<h2>";
		}
	}
   echo "</div>";
   
   ?>
	</div>
	</div><!-- row -->
   <script>
/* JQuery to toggle sidebar on main page */
	$("#menu-toggle").click(function(expansion){
		expansion.preventDefault();
			$("#wrapper").toggleClass("menuHidden");
			$(".glyphicon.glyphicon-arrow-up").fadeToggle(1000);
		});	
	</script>
</body>
</html>
<?php
   }
   ?>