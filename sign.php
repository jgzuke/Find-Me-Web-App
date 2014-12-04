<?php
header("Location: /");
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
     $db = new pdo('mysql:host=127.0.0.1:8889;dbname=itemlist', 'root', 'temppass');
  }catch(PDOException $ex){
      die(json_encode(
          )
      );
  }
}
try {
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
} catch (PDOException $ex) {
  // Log error.
}
$db = null;
?>