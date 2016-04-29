<?php
session_start();
include ("dbConnect.php");
//checks if ?logout has been passed via URL
if(isset($_GET["logout"])){
	
	unset($_SESSION["currentUser"]);
	unset($_SESSION["currentUserID"]);
}


//if current user isn't set, unset ID (just safety)
if (!isset($_SESSION["currentUser"])){
	
	unset($_SESSION["currentUserID"]);
}

//if signup form has been submitted, check user/password
if (isset($_POST["action"]) && $_POST["action"]=="signup") {

	$signupUser=$_POST["sUsername"];
	$signupPass=$_POST["sPassword"];
	
	$dbQuery2=$db->prepare("select * from users
							where username=:signupUser");
	$dbParams2 = array('signupUser'=>$signupUser);
	$dbQuery2->execute($dbParams2);
	$dbRow2=$dbQuery2->fetch(PDO::FETCH_ASSOC);
	
	//if to check if name is taken
	if($dbRow2["username"]==$signupUser){
		echo"<script>alert('Account name taken');</script>";
	}else{
	
	//Insert query to create account 
	$dbQuery=$db->prepare("insert into users 
							values (null, :sUsername, :sPassword)"); 
	$dbParams = array('sUsername'=>$signupUser, 'sPassword'=>$signupPass);
	$dbQuery->execute($dbParams);
	
	echo"<script>alert('Account Created!');</script>";
	
	$_SESSION["currentUser"]=$signupUser;
	}
}
//if login form has been submitted, check user/password
if (isset($_POST["action"]) && $_POST["action"]=="login") {

	//set form values to PHP variables for validation
	$formUser=$_POST["username"];
	$formPass=$_POST["password"];

	//check users in database
	$dbQuery=$db->prepare("select * from users where username=:formUser"); 
	$dbParams = array('formUser'=>$formUser);
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
      if ($dbRow["username"]==$formUser) {       
         if ($dbRow["password"]==$formPass) {
            $_SESSION["currentUser"]=$formUser;
            $_SESSION["currentUserID"]=$dbRow["id"];
			header("Location: main.php");
				/*if (isset($_SESSION["tracklist"])) 
                 header("Location: addToBasket.php");
            else header("Location: shopForTracks.php");  		*/	
         }//password if
         else {
            header("Location: main.php?failCode=2");
         }
      }//user if 
	  else {
            header("Location: main.php?failCode=1");
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
<link rel="stylesheet" href="main_css.css">
</head>
<body>



<!-- navbar --> 
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid" id = "header">
    <div class="navbar-header">
      <a class="navbar-brand" href="#" id="menu-toggle"><span class="glyphicon glyphicon-arrow-down" id="menu-arrow"></span> Bean Industries</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#"><span class="glyphicon glyphicon-home"></span> Home</a></li>
      <li><a href="gamestore.php"><span class="glyphicon glyphicon-off"></span> Game Store</a></li>
      <li><a href="#"><span class="glyphicon glyphicon-earphone"></span> Contact Us</a></li>
    </ul>
	
	<?php
	if (isset($_SESSION["currentUser"])) { 
	
	$userID = $_SESSION["currentUser"];
	
    $dbQuery=$db->prepare("select * from basket where paid='N' and userID=:userID");
    $dbParams = array('userID'=>$_SESSION["currentUser"]);
    $dbQuery->execute($dbParams);
    $numGames=$dbQuery->rowCount();
	}
	
	if(!isset($_SESSION["currentUser"])){
	// right side of navbar-
	echo "<ul class=\"nav navbar-nav navbar-right\">";
		echo "<li><a href=\"#\" data-toggle=\"modal\" data-target=\"#myModal\"><span class=\"glyphicon glyphicon-user\"></span> Sign Up</a></li>";
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
		&nbsp;&nbsp;<a href="main.php?logout">Log out</a>	
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
		<form role="form" method = "post" action="main.php">
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
        <form role="form" name="login" method ="post" action="main.php">
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
<!-- Twitter API -->
	<div id = "twitterapi">
	   <div id="container">  	   	   	   
      	   <form method='get' action='easytweet (1).php'>
			   <input type="radio" name="searchVal" value = "5"> 5<br>
			   <input type="radio" name="searchVal" value = "10"> 10<br>
			   <input type="radio" name="searchVal" value = "15"> 15<br>
			   <input type="radio" name="searchVal" value = "20"> 20<br>
      	       <input type='text' name='searchTerm' value='' size='15'>
      	       <input type='submit' name='action' value='Search'>
      	   </form> 
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
		$searchVal="20";
		$searchTerm="uncharted";
		
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
	</div> <!-- Twitter API -->
</div><!-- Sidebar Wrapper -->


<!-- PAGE CONTENT -->
<div id = "page-content-wrapper">
<div class="row">
	<div class="container-fluid">
		
			<div class="well">
				<h1>New Releases</h1>
				<h1><small>Check out our hot new releases with <kbd>KILLER PRICES</kbd></small></h1>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-4">
			<!-- Empty Space -->
		</div>
		
		<?php				
<<<<<<< HEAD
		$dbQuery=$db->prepare("select id, url from gamelist");       
=======
		$dbQuery=$db->prepare("select url from gamelist");       
>>>>>>> origin/master
		$dbQuery->execute();
		$numTracks=$dbQuery->rowCount();

		($dbRow=$dbQuery->fetch(PDO::FETCH_NUM));
		?>
		
<<<<<<< HEAD
	<!-- Front Page Image CSS Div -->
	<div class="front-image">
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
			<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			<li data-target="#myCarousel" data-slide-to="1"></li>
			<li data-target="#myCarousel" data-slide-to="2"></li>
			<li data-target="#myCarousel" data-slide-to="3"></li>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner" role="listbox">
			<div class="item active">
			  <a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" alt="First"></a>
			</div>

			<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
			<div class="item">
			  <a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" alt="Second"></a>
			</div>
			
			<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
			<div class="item">
			  <a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" alt="Third"></a>
			</div>

			<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
			<div class="item">
			  <a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" alt="Four"></a>
			</div>
		  </div>
			<?php //} ?>

		  <!-- Left and right controls -->
		  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
=======
		<!-- Front Page Image CSS Div -->
		<div class="front-image">
			<div id="myCarousel" class="carousel slide" data-ride="carousel">
			  <!-- Indicators -->
			  <ol class="carousel-indicators">
				<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
				<li data-target="#myCarousel" data-slide-to="1"></li>
				<li data-target="#myCarousel" data-slide-to="2"></li>
				<li data-target="#myCarousel" data-slide-to="3"></li>
			  </ol>

			  <!-- Wrapper for slides -->
			  <div class="carousel-inner" role="listbox">
				<div class="item active">
				  <a href="#"><img src="<?php echo "$dbRow[0]";?>" alt="First"></a>
				</div>

				<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
				<div class="item">
				  <a href="#"><img src="<?php echo "$dbRow[0]";?>" alt="Second"></a>
				</div>
				
				<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
				<div class="item">
				  <a href="#"><img src="<?php echo "$dbRow[0]";?>" alt="Third"></a>
				</div>

				<?php ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)); ?>
				<div class="item">
				  <a href="#"><img src="<?php echo "$dbRow[0]";?>" alt="Four"></a>
				</div>
			  </div>
				<?php //} ?>

			  <!-- Left and right controls -->
			  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			  </a>
			  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			  </a>
>>>>>>> origin/master
			</div><!-- Carousel -->
		</div><!-- Front image div -->
	</div><!-- Row -->		
</div><!-- Content Wrapper -->

<!-- Main wrapper -->
</div>

<!-- Menu toggle -->
<script>
/* JQuery to toggle sidebar on main page */
	$("#menu-toggle").click(function(expansion){
		expansion.preventDefault();
			$("#wrapper").toggleClass("menuDisplayed");
			$(".glyphicon.glyphicon-arrow-down").fadeToggle(1000);
		});	
	</script>
</body>
</html>
<?php
   }
?>