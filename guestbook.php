<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find Me</title>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,600,700" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<html>
 <body>
 <?php
  use google\appengine\api\users\User;
  use google\appengine\api\users\UserService;
  $user = UserService::getCurrentUser();
  if ($user) {
    //echo 'Hello, ' . htmlspecialchars($user->getNickname());
  }
  else {
    header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
  }
  ?>
  <h1 id="topname">Find Me</h1>
  <hr width="100%"  background-color="#FFFFFF" size="4" height = "2px"></hr>
    <div class="row">
      <div id = "Find" class="col-md-6">
        <h2>Find Item</h2>
        <div id = "searchForm">
          <form action="#" method="post">
            <input type="text" name="query"> 
            <input type="submit" value="Find" name = "search">
          </form>
        </div>
        <?php
          if(isset($_POST['search']))
          {
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
                      echo "<div><strong>".$item."</strong>: ".$location."</div>";
              } 
          } else
          {
            $db = null;
            if (isset($_SERVER['SERVER_SOFTWARE']) &&
            strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
              // Connect from App Engine.
              try{
                 $db = new pdo('mysql:unix_socket=/cloudsql/findmewebapp:cloudinstanceid;dbname=guestbook', 'root', 'temppass');
              }catch(PDOException $ex){
                  die(json_encode(
                      array('outcome' => false, 'message' => 'Unable to connect.')
                      )
                  );
              }
            } else {
              // Connect from a development environment.
              try{
                 $db = new pdo('mysql:host=127.0.0.1:8889;dbname=guestbook', 'root', 'temppass');
              }catch(PDOException $ex){
                  die(json_encode(
                      array('outcome' => false, 'message' => 'Unable to connect')
                      )
                  );
              }
            }
            try {
              // Show existing guestbook entries.
              foreach($db->query('SELECT * from entries') as $row) {
                      echo "<div><strong>".$row['myItemName']."</strong>: ".$row['myItemLocation'] . "</div>";
               }
            } catch (PDOException $ex) {
              echo "An error occurred in reading or writing to guestbook.";
            }
            $db = null;
          }
          ?>
      </div>
      <div id = "Move" class="col-md-6">
        <h2>Move Item</h2>
        <div id = "submitForm">
          <form action="/sign" method="post">
            <div><textarea name="name" rows="1" cols="30"></textarea></div>
            <div><textarea name="location" rows="1" cols="30"></textarea></div>
            <div><input type="submit" value="Move Item"></div>
          </form>
        </div>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js-image-slider.js" type="text/javascript"></script>
  </body>
</html>