<?php
header('Location: '."/");
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
  if (array_key_exists('name', $_POST))
  {
    $query = $db->prepare("SELECT  myItemName, myItemLocation FROM entries WHERE myItemName LIKE '%" . $name .  "%'"); 
    $query->execute();  
    if (!$query->rowCount() == 0)
    {
      while ($results = $query->fetch())
      {
          echo "<div><strong>".$results['myItemName']."</strong>: ".$results['myItemLocation'] . "</div>";
      }
    } else {
      echo 'Nothing found';
    }
    echo "<h1></h1>";


    $stmt = $db->prepare('INSERT INTO entries (myItemName, myItemLocation) VALUES (:name, :location)');
    $stmt->execute(array(':name' => htmlspecialchars($_POST['name']), ':location' => htmlspecialchars($_POST['location'])));
    $affected_rows = $stmt->rowCount();
    // Log $affected_rows.
  }
} catch (PDOException $ex) {
  // Log error.
}
$db = null;
?>