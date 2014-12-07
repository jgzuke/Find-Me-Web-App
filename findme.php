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
if(!isset($user))
{
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
    $dataTableName = $myDataTable;
    $sql = "INSERT INTO $myTableName (itemTableName, itemTableShort) VALUES (:name, :short)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':name' => $dataTableName, ':short' => 'default'));
    $affected_rows = $stmt->rowCount();
}
$results=$db->query("SHOW TABLES LIKE '$currentTable'"); // if there isnt a default items table for user make one
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
if(isset($_POST['delete']))
{
  $name=$_POST['todelete'];
  $query = $db->prepare("SELECT itemTableName FROM $myTableName WHERE itemTableShort = '$name'"); 
  $query->execute();
  if (!$query->rowCount() == 0)
  {
    while($results=$query->fetch())
    {
      $deleteTable = $results['itemTableName'];
      echo $deleteTable;

      $stmt = $db->prepare("DROP TABLE IF EXISTS $deleteTable");
      $stmt->execute();
    }
  }
  $sql="DELETE FROM $myTableName WHERE itemTableShort = '$name'";
  $stmt=$db->prepare($sql);
  $stmt->execute();
}
$name = substr($_SERVER['REQUEST_URI'], 1);
if(empty($name)) $name='default';
$query=$db->prepare("SELECT itemTableShort, itemTableName FROM $myTableName WHERE itemTableShort = '$name'");
$query->execute();
if(!$query->rowCount()==0)
{
    while($results=$query->fetch()) {
        $currentTable = $results['itemTableName'];
    }
}
if(isset($_POST['moving']))
{
  try
  {
    $currentTable=$_POST['tabletouse'];
    if (array_key_exists('name', $_POST))
    {
      $name=$_POST['name'];
      $query = $db->prepare("SELECT  myItemName, myItemLocation FROM $currentTable WHERE myItemName = '$name'"); 
      $query->execute();
      if (!$query->rowCount() == 0)
      {
          $sql = "UPDATE $currentTable SET myItemLocation='".$_POST['location']."' WHERE myItemName = '$name'";
          $stmt = $db->prepare($sql);
          $stmt->execute();
      } else
      {
        $sql = "INSERT INTO $currentTable (myItemName, myItemLocation) VALUES (:name, :location)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':name' => htmlspecialchars($_POST['name']), ':location' => htmlspecialchars($_POST['location'])));
        $affected_rows = $stmt->rowCount();
      }
      $sql = "DELETE FROM $currentTable WHERE myItemName LIKE '' OR myItemLocation LIKE ''"; 
      $stmt = $db->prepare($sql);
      $stmt->execute();
    }
  } catch (PDOException $ex) {}
}
?>
    <nav class="navbar navbar-default navbar-fixed-top shadow" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="">Find Me</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
              <?php
                foreach($db->query("SELECT * FROM $myTableName") as $row)
                {
                  if($row['itemTableShort'] == substr($_SERVER['REQUEST_URI'], 1))
                  {
                    echo "<li class='active'><a href=".$row['itemTableShort'].">".$row['itemTableShort']."</a></li>";
                  } else
                  {
                    echo "<li><a href=".$row['itemTableShort'].">".$row['itemTableShort']."</a></li>";
                  }
                }
               ?>
               <li><button type="button" id = "deletelist" style="color: #777" class="btn btn-default navbar-btn" data-toggle="modal" data-target="#deleteListModal">Delete List</button></li>
               <li><button type="button" class="btn btn-primary navbar-btn" data-toggle="modal" data-target="#newListModal">New List</button></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <?php
            echo "<li><a href=".UserService::createLogoutUrl($_SERVER['REQUEST_URI']).">Logout</a><li>";
               ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" id = "mymargin"></div>
    <div class="row" id = "myrows">
      <div class="col-md-6" id = "findrow">
        <h1 class="mytext">Find Item</h1>
        <div class = "myforms">
          <form role="form" action="" method="post">
            <div class="form-group">
              <label for="lookingfor">What are you looking for</label>
              <input type="text" class="form-control" placeholder="Enter name" name="query" value="item" id="lookingfor">
            </div>
            <div><button type="submit" class="btn btn-primary" value="Find" name = "search">Find</button>
            <button type="submit" class="btn btn-default" style="color: #777" value="Show All" name = "showall">Show All</button>
            <button type="submit" class="btn btn-default" style="color: #777" value="Delete" name = "delete">Delete</button></div>
          </form>
        </div>
        <?php
if(isset($_POST['search'])) {
    $name=$_POST['query'];
    $query=$db->prepare("SELECT  myItemName, myItemLocation FROM $currentTable WHERE myItemName LIKE '%" . $name .  "%'");
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
if(isset($_POST['showall'])) {
  try {
      $name=(string) $currentTable;
      foreach($db->query("SELECT * FROM $name") as $row) {
          echo "<div><strong>" . $row['myItemName'] . "</strong>: " . $row['myItemLocation'] . "</div>";
      }
  }
  catch(PDOException $ex) {
      echo "An error occurred in reading or writing to guestbook.";
  }
}
?>
     </div>
      <div class="col-md-6" id = "findrow">
        <h1 class="mytext">Move Item</h1>
        <div class = "myforms">
          <form role="form" action="" method="post">
            <div class="form-group">
              <label for="whatisit">What did you move</label>
              <input type="text" class="form-control" placeholder="Enter name" name="name" value="item" id="whatisit">
            </div>
            <div class="form-group">
              <label for="whereisit">Where is it now</label>
              <input type="text" class="form-control" placeholder="Enter name" name="location" value="location" id="whereisit">
            </div>
            <input type='hidden' name='tabletouse' value="<?php echo "$currentTable"; ?>"></input>
            <div><button type="submit" class="btn btn-primary" value="Move Item" name = "moving">Move Item</button></div>
          </form>
        </div>
      </div>
    </div>
<div class="modal fade" id="deleteListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" background="#cccccc">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h2 class="modal-title" id="myModalLabel">Delete List</h2>
      </div>
      <div class="modal-body">
      <?php
        foreach($db->query("SELECT * FROM $myTableName") as $row)
        {
          echo "<form role='form' action='' method='post'> <div class='form-group'>
                          <input type='hidden' name='todelete' value=".$row['itemTableShort']."></input>
                          <button type='submit' class='btn btn-primary' value='DeleteDB' name = 'delete'>".$row['itemTableShort']."</button>
                    </div> </form>";
        }
      ?>
        
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="newListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h2 class="modal-title" id="myModalLabel">New List</h2>
      </div>
      <div class="modal-body">
          <form role="form" action="/makenewlist" method="post">
            <div class="form-group">
              <label for="basename">List Name</label>
              <input type="text" class="form-control" placeholder="Enter name" name="name" value="name" id="basename">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Friends</label>
              <input type="email" class="form-control" placeholder="Enter email" name="email1" value="email1" id="exampleInputEmail1">
            </div>
            <div class="form-group">
              <input type="email" class="form-control" placeholder="Enter email" name="email2" value="email2" id="exampleInputEmail2">
            </div>
            <div class="form-group">
              <input type="email" class="form-control" placeholder="Enter email" name="email3" value="email3" id="exampleInputEmail3">
            </div>
            <div><button type="submit" class="btn btn-primary" value="Create" name = "Create">Create</button></div>
          </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="foundItemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h2 class="modal-title" id="myModalLabel">Where is it?</h2>
      </div>
      <div class="modal-body">
          <h3>What What</h3>
      </div>
    </div>
  </div>
</div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js-image-slider.js" type="text/javascript"></script>
    <script src="js/myjs.js"></script>
    <?php $db=null; ?>
  </body>
</html>