<?php
header('Location: '."/");
$db = null;
if (isset($_SERVER['SERVER_SOFTWARE']) &&
strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
  // Connect from App Engine.
  try{
     $db = new pdo('mysql:unix_socket=/cloudsql/findmewebapp:cloudinstanceid;dbname=itemlist', 'root', '');
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
          )
      );
  }
}
try {
  if (array_key_exists('name', $_POST))
  {
    $name=$_POST['name'];
    $query = $db->prepare("SELECT  myItemName, myItemLocation FROM entries WHERE myItemName = '$name'"); 
    $query->execute();
    if (!$query->rowCount() == 0)
    {
        $sql = "UPDATE entries SET myItemLocation='".$_POST['location']."' WHERE myItemName = '$name'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    } else
    {
      $stmt = $db->prepare('INSERT INTO entries (myItemName, myItemLocation) VALUES (:name, :location)');
      $stmt->execute(array(':name' => htmlspecialchars($_POST['name']), ':location' => htmlspecialchars($_POST['location'])));
      $affected_rows = $stmt->rowCount();
    }
    $sql = "DELETE FROM entries WHERE myItemName LIKE '' OR myItemLocation LIKE ''"; 
    $stmt = $db->prepare($sql);
    $stmt->execute();
  }
} catch (PDOException $ex) {
  // Log error.
}
$db = null;
?>