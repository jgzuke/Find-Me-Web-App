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
$user=UserService::getCurrentUser();
$tempName=$user->getEmail();
$myTableName=preg_replace('/[^A-Za-z0-9\-]/', '', $tempName);
$currentTable=$myTableName . 'default';
if(isset($user)) {
    echo sprintf('<a href="%s" id="logoutbutton">Logout</a>', UserService::createLogoutUrl($_SERVER['REQUEST_URI']));
} else {
    UserService::createLogoutUrl($_SERVER['REQUEST_URI']);
}
$db=null;
if(isset($_SERVER['SERVER_SOFTWARE'])&&strpos($_SERVER['SERVER_SOFTWARE'], 'Google App Engine')!==false) {
    try {
        $db=new pdo('mysql:unix_socket=/cloudsql/findmewebapp:cloudinstanceid;dbname=itemlist', 'root', '');
    }
    catch(PDOException $ex) {
        die(json_encode(array(
            'outcome'=>false,
            'message'=>'Unable to connect.'
        )));
    }
} else {
    try {
        $db=new pdo('mysql:host=127.0.0.1:8889;dbname=itemlist', 'root', 'temppass');
    }
    catch(PDOException $ex) {
    }
}
$myDataTable=$myTableName . 'default';
$results=$db->query("SHOW TABLES LIKE '$myTableName'"); // if there isnt a table to store users table names make one
if($results->rowCount()==0) {
    $sql="CREATE table $myTableName(itemTableName VARCHAR(80) NOT NULL, itemTableShort VARCHAR(30) NOT NULL);";
    $db->exec($sql);
    $dataTableName = "'".$myDataTable."'";
    $sql = "INSERT INTO $myTableName (itemTableName, itemTableShort) VALUES (:name, :short)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':name' => $dataTableName, ':short' => 'default'));
    $affected_rows = $stmt->rowCount();
}
$results=$db->query("SHOW TABLES LIKE '$myDataTable'"); // if there isnt a default items table for user make one
if($results->rowCount()==0) {
    $sql="CREATE table $currentTable(myItemName VARCHAR(30) NOT NULL, myItemLocation VARCHAR(30) NOT NULL);";
    $db->exec($sql);
}
if(isset($_POST['delete'])) {
    $name=$_POST['query'];
    $sql="DELETE FROM $currentTable WHERE myItemName = '$name'";
    $stmt=$db->prepare($sql);
    $stmt->execute();
}
if(isset($_POST['opentable']))
{
    
}
?>
 <h1 id="topname">Find Me</h1>
 <?php
  foreach($db->query("SELECT * FROM $myTableName") as $row)
  {
      echo '<form action="" method="post">
          <input type="submit" value='.$row['itemTableShort'].' name = "opentable"></div>
        </form>';
  }
 ?>
  <hr width="100%"  background-color="#FFFFFF" size="4" height = "2px"></hr>
    <div class="row">
      <div class="col-md-6">
        <h2>Find Item</h2>
        <div class = "myforms">
          <form action="" method="post">
            <div><input type="text" name="query" value="item"></div>
            <div><input type="submit" value="Find" name = "search">
            <input type="submit" value="Delete" name = "delete"></div>
          </form>
        </div>
        <?php
if(isset($_POST['search'])) {
    $name=$_POST['query'];
    $query=$db->prepare("SELECT  myItemName, myItemLocation FROM $currentTable WHERE myItemName = '$name'");
    $query->execute();
    if(!$query->rowCount()==0) {
        while($results=$query->fetch()) {
            echo "<div><strong>" . $results['myItemName'] . "</strong>: " . $results['myItemLocation'] . "</div>";
        }
    } else {
        echo 'Nothing found';
    }
    echo "<h1></h1>";
}
try {
    $name=(string) $currentTable;
    foreach($db->query("SELECT * FROM $name") as $row) {
        echo "<div><strong>" . $row['myItemName'] . "</strong>: " . $row['myItemLocation'] . "</div>";
    }
}
catch(PDOException $ex) {
    echo "An error occurred in reading or writing to guestbook.";
}
?>
     </div>
      <div class="col-md-6">
        <h2>Move Item</h2>
        <div class = "myforms">
          <form action="/sign" method="post">
            <div><input name="name" value="item" type="text"></input></div>
            <div><input name="location" value="location" type="text"></input></div>
            <div><input type="submit" value="Move Item"></div>
          </form>
        </div>
      </div>
    </div>
    <form action="/newlist" id = "newlistform">
      <input type="submit" value="New List">
    </form>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js-image-slider.js" type="text/javascript"></script>
    <?php $db=null; ?>
  </body>
</html>