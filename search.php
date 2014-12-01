<?php
    mysql_connect("mysql:host=127.0.0.1:8889", "root", "temppass") or die("Error connecting to database: ".mysql_error());
    mysql_select_db("guestbook") or die(mysql_error());
     
     
     
?>