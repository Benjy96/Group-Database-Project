<?php
session_start();
include ("dbConnect.php");

if(isset($_SESSION["currentUser"])){
	$userID = $_SESSION["currentUser"];
}
//checks if ?logout has been passed via URL
if(isset($_GET["logout"])){
	
	unset($_SESSION["currentUser"]);
	unset($_SESSION["currentUserID"]);
}

//if current user isn't set, unset ID (just safety)
if (!isset($_SESSION["currentUser"])){
	
	unset($_SESSION["currentUserID"]);
}

//if individual game added to basket
if(isset($_POST["action"]) && $_POST["action"] == "addGame"){
	if($_POST["selectedGame"] == ""){
		echo "<script>alert('Please confirm your selection');</script>";
	} else {
	if($_SESSION["currentUser"] != ""){
		
	$userID=$_SESSION["currentUser"];
		
	$addedGame=$_POST["selectedGame"];
	
	$dbQuery=$db->prepare("select * 
							from basket
							where userID=:userID and gameID=:addedGame");
	$dbParams = array('userID'=>$userID, 'addedGame'=>$addedGame);
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
	
	//if to check if name is taken
	if($dbRow["gameID"]==$addedGame){
		echo"<script>alert('Item already in basket');</script>";
	}
	else{
	//Insert query
	$dbQuery2=$db->prepare("insert into basket 
							values (null, :userID, :addedGame, 'N')"); 
	$dbParams2 = array('userID'=>$userID, 'addedGame'=>$addedGame);
	$dbQuery2->execute($dbParams2);	
	}//else
		
	//add success text
	}
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
	}
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
      <a class="navbar-brand" href="#" id="menu-toggle"><span class="glyphicon glyphicon-arrow-down" id="menu-arrow"></span> Bean Industries</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="main.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
      <li class="active"><a href="gamestore.php"><span class="glyphicon glyphicon-off"></span> Game Store</a></li>
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
		&nbsp;&nbsp;<a href="gamestore.php?logout">Log out</a>	
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
	<ul class="sidebar-nav" id = "header-right">
		<br>
		<li><a href ="#">Account</a></li>
		<li><a href ="#">Account</a></li>
	</ul>
	<input type="text" class="form-control" placeholder="Search Games..." id ="searchbar">
</div>


<!-- PAGE CONTENT -->
<div id = "page-content-wrapper">

	<?php 
	//if game has been selected, go to information page
	if(isset($_GET["gameno"])){

	$gameNum = $_GET["gameno"];
		
	//QUERY FOR IMAGE AND GAMES
	$dbQuery=$db->prepare("select id, title, ageRating, price, url from gamelist");       
	$dbQuery->execute();
	$numTracks=$dbQuery->rowCount();
	
	//while loop breaks once it reaches correct game
	while ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)){
		if($dbRow[0] == $gameNum)
			break;
	}
	?>
<div class="row">
<br>
<br>
<br>
	<div class="col-md-2">
	<!-- Spacing -->
	</div>
	<div class="col-md-2">
	<div class="front-image">
		<img src="<?php echo "$dbRow[4]" ?>" width = "300" height = "423">
	</div>
	</div>
	
	<div class="col-md-6">
	<h3>Name: </h3><?php echo "<h3><small>$dbRow[1]</small></h3>"; ?>
	<h3>Age Rating: </h3><?php echo "<h3><small>$dbRow[2]</small></h3>"; ?>
	<h3>Price: </h3><?php echo "<h3><small>£$dbRow[3]</small></h3>"; ?>
	<br>
	<br>
	<br>
	<br>
	<br>
		<form role="form" name="addGame" method="post" action="gamestore.php">
			<div class="checkbox">
			<label><input type="checkbox" name="selectedGame" value="<?php echo $dbRow[0];?>">Confirm Selection</label>
			</div>
			<input type="hidden" name="action" value="addGame">
			<div id="basket-button">
			<!--<button type="submit" id="basket-button" class="btn btn-default">Add Game To Basket</button>-->
			</div>
		</form>
</div>
	<?php	
	}else {
	?>
	<!-- AJAX Search -->
	<script type="text/javascript">
  
     var ajaxObject = getXmlHttpRequestObject();

     function getXmlHttpRequestObject() {
       if (window.XMLHttpRequest)
          return new XMLHttpRequest();
       else if (window.ActiveXObject) 
            return new ActiveXObject("Microsoft.XMLHTTP");
       else alert ("XMLHttp not supported by browser")
     }
	 
	 //the modified stuff from ajax recipie
     function searchGames() { 
       if (ajaxObject.readyState==4 || ajaxObject.readyState==0) {
          var str=escape(document.getElementById("txtSearch").value);
          ajaxObject.open("GET","getGames.php?action=games&search="+str, true);
          ajaxObject.onreadystatechange=displayGames;
          ajaxObject.send(null);         
       }
     }
     
       
     function listGames(letter) {
       if (ajaxObject.readyState==4 || ajaxObject.readyState==0) {
          ajaxObject.open("GET","getGames.php?letter="+letter, true);
          ajaxObject.onreadystatechange=displayGames;
          ajaxObject.send(null);
       }
     }

     function displayGames() { 
       if (ajaxObject.readyState==4) {
		  var gamesArray=ajaxObject.responseText.split("\n");
          var numGames=gamesArray[0];
          var lastGame=parseInt(gamesArray[1])+1;
          var htmlStr="<form method=\"post\" action=\"addToBasket.php\">";
          htmlStr+="<ul>";          
          for(var i=1; i<=numGames; i++) {
            gameDetails=gamesArray[i].split("_");
            htmlStr+="<li id=\"ajax-list\"><input type=\"checkbox\" name=\"games[]\" value=\""+gameDetails[0]+"\"> "+gameDetails[1]+"</li>";         
          }     
          htmlStr+="</ul>";
          htmlStr+="<input type=\"submit\" value=\"Add selected games to basket\">";
          htmlStr+="</form>"; 
          document.getElementById("games").innerHTML=htmlStr;
       }
     }  
  </script>
  <!-- Well for ajax search -->
  <div class="well">
  <div class="container-fluid">
  <div class="col-md-4">
	<h1>Search</h1>
	<?php
   $letters="abcdefghijklmnopqrstuvwxyz0123456789";
   echo "<div class=\"bigMargin\">";
   echo "<h2><small>Choose Game: </small></h2>";
   for ($i=0; $i<=50; $i++) {
      $letter=substr($letters,$i,1);
      $initial=$letter."%";
      $dbQuery=$db->prepare("select * from gamelist where title like :initial order by title asc");
      $dbParams = array('initial'=>$initial);
      $dbQuery->execute($dbParams);
      if ($dbQuery->rowCount()>0)
         echo "<a href=\"#\" onclick=\"listGames('$letter')\">$letter</a> ";
      else echo "$letter ";   
   }  
   echo "</div>";

?>
	<form onsubmit="return false">
     <input type="text" class="form-control" id="txtSearch" onkeyup="searchGames()" autocomplete="off" value="">
  </form>  
  </div>
  <div class="col-md-4">
	<div id="games">
	</div>
  </div>
  </div>
  </div>
	<?php	
	//QUERY FOR IMAGE AND GAMES
	$dbQuery=$db->prepare("select id, url from gamelist");       
	$dbQuery->execute();
	$numTracks=$dbQuery->rowCount();
	?>
		
	<?php for($i = 0; $i < 25; $i++){ ?>
	<div class="row">
	<br>
	<br>
	<br>
	<div class="container">
	<div class="col-md-8">
		<div class="front-image">
		<!-- IMAGE BLOCK -->
		<?php 
		($dbRow=$dbQuery->fetch(PDO::FETCH_NUM));
		?>
		<a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" id="gamepic" alt="First"></a>
		
		</div>
	</div><!-- container -->
	<div class="col-md-4">
		<div class="front-image">
		<!-- IMAGE BLOCK -->
		<?php 
		($dbRow=$dbQuery->fetch(PDO::FETCH_NUM));
		?>
		<a href="gamestore.php?gameno=<?php echo "$dbRow[0]"?>"><img src="<?php echo "$dbRow[1]";?>" id="gamepic" alt="First"></a>
		</div>
	</div>
</div>
</div><!-- row -->
	<?php }} ?>
		
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
/* JQuery to toggle button on basket page */
	var buttonCounter = 0;
	$(".checkbox").click(function(e){
		buttonCounter++;
		if(buttonCounter<2)
		$("#basket-button").append("<button type=\"submit\" class=\"btn btn-default\">Add Game To Basket</button>");
	});
	</script>
</body>
</html>
<?php
}
?>