<?php
session_start();

unset($_SESSION["currentUser"]);
unset($_SESSION["currentUserID"]);

if (isset($_POST["action"]) && $_POST["action"]=="login") {

	$formUser=$_POST["username"];
	$formPass=$_POST["password"];

	include ("dbConnect.php");

	$dbQuery=$db->prepare("select * from users where username=:formUser"); 
	$dbParams = array('formUser'=>$formUser);
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
      if ($dbRow["username"]==$formUser) {       
         if ($dbRow["password"]==$formPass) {
            $_SESSION["currentUser"]=$formUser;
            $_SESSION["currentUserID"]=$dbRow["id"];	
				if (isset($_SESSION["tracklist"])) 
                 header("Location: addToBasket.php");
            else header("Location: shopForTracks.php");  			
         }
         else {
            header("Location: main.php?failCode=2");
         }
      } else {
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
      <li><a href="#"><span class="glyphicon glyphicon-off"></span> Game Store</a></li>
      <li><a href="#"><span class="glyphicon glyphicon-earphone"></span> Contact Us</a></li>
    </ul>
	
	<!-- right side of navbar-->
	<ul class="nav navbar-nav navbar-right">
		<li><a href="#" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
		<li><a href="#" data-toggle="modal" data-target="#myModalLogin"><span class="glyphicon glyphicon-log-in"></span> Login </a></li>
	</ul>
  </div><!-- fluid container -->  
</nav><!-- NAV BAR TOP OF PAGE -->

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
		<form role="form" name="signup" method = "post" action="main.php">
			<div class="form-group">
				<label for="usr">Username:</label>
				<input type="text" class="form-control" name="sUsername" placeholder="Enter Name...">
			</div>
			
			<div class="form-group">
				<label for="pwd">Password:</label>
				<input type="password" class="form-control" name="sPassword"placeholder="Enter Password...">
			</div>
			<!-- Submit button -->
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
	<ul class="sidebar-nav" id = "header-right">
		<br>
		<li><a href ="#">Account</a></li>
		<li><a href ="#">Account</a></li>
	</ul>
	<input type="text" class="form-control" placeholder="Search Games..." id ="searchbar">
</div>


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
		
		<!-- Front Page Image CSS Div -->
		<div class="front-image">
			<img src = "gow.jpg">
		</div>
	</div>
		
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