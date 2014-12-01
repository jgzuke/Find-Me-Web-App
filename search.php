<?php
$name=$_POST['query']; 
   //connect  to the database 
   $db=mysql_connect  ("127.0.0.1:8889", "root",  "temppass") or die ('I cannot connect to the database  because: ' . mysql_error()); 
   //-select  the database to use 
   $mydb=mysql_select_db("guestbook"); 
    //-query  the database table 
    $sql="SELECT  myItemName, myItemLocation FROM entries WHERE myItemName LIKE '%" . $name .  "%'"; 
    //-run  the query against the mysql query function 
    $result=mysql_query($sql); 
    //-create  while loop and loop through result set 
    while($row=mysql_fetch_array($result)){ 
            $item  =$row['myItemName']; 
            $location=$row['myItemLocation']; 
            echo "<p> ".$item . " " . $location;
    } 
?>
