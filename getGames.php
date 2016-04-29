<?php
   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
   header('Cache-Control: no-store, no-cache, must-revalidate');
   header('Cache-Control: post-check=0, pre-check=0', FALSE);
   header('Prama: no-cache');
   // the above headers will prevent the page output from being cached

   include ("dbConnect.php");
   //this is also from the ajax thing
   if (isset($_GET["action"]) && $_GET["action"]=="games") { 
      $keyword=$_GET["search"];

      $dbQuery=$db->prepare("select id, title from gamelist where title like :keyword order by title asc limit 100");
      $keyword=$keyword."%";
      $dbParams=array('keyword'=>$keyword);
      $dbQuery->execute($dbParams);
      echo $dbQuery->rowCount()."\n";
      while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
         echo $dbRow["id"]."_".$dbRow["title"]."\n";
      }
   }
   
   
   
   $letter=$_GET["letter"]."%";
   $dbQuery=$db->prepare("select id,title from gamelist where title like :letter order by title asc");
   $dbParams = array('letter'=>$letter);
   $dbQuery->execute($dbParams);
   echo $dbQuery->rowCount()."\n";
   while ($dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC)) {
      echo $dbRow["id"]."_".$dbRow["title"]."\n";
   }  
   

?>