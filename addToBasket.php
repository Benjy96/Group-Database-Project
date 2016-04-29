<?php
   session_start();
  
   if (isset($_SESSION["gamelist"])) {
      $games=explode("^",$_SESSION["gamelist"]);
      unset($_SESSION["gamelist"]);
   } else {
      $games=$_POST["games"];
   }

   if (isset($_SESSION["currentUser"])) {
      include ("dbConnect.php");
      $userID=$_SESSION["currentUserID"];
      foreach ($games as $thisGameID) {
          $dbQuery=$db->prepare("select * from basket where userID=:userID and gameID=:thisGameID");
          $dbParams = array('userID'=>$userID, 'thisGameID'=>$thisGameID);
          $dbQuery->execute($dbParams);
          if ($dbQuery->rowCount()==0) {
             $dbQuery=$db->prepare("insert into basket 
									values (null, :userID, :thisGameID, 'N')");
             $dbParams = array('userID'=>$userID, 'thisGameID'=>$thisGameID);
             $dbQuery->execute($dbParams);
          }   
      }
      header("Location: gamestore.php"); 
   } else {
      $_SESSION["gamelist"]="";
      foreach ($games as $thisGameID) {
         $_SESSION["gamelist"].=$thisGameID."^";
      }
      $_SESSION["gamelist"]=rtrim($_SESSION["gamelist"],"^");
      header("Location: main.php?logout");
   }   
?>