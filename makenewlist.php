<?php
header('Location: '."/");
use google\appengine\api\users\User;
use google\appengine\api\users\UserService;
$user = UserService::getCurrentUser();
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
$userName = $user->getEmail();
$myUserName = preg_replace('/[^A-Za-z0-9\-]/', '', $userName);
$firstUserName = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['email1']);
$secondUserName = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['email2']);
$thirdUserName = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['email3']);
$dataTableName = "'".$myUserName.$_POST['name']."'";
$dataTableShort = "'".$_POST['name']."'";


$newTableName = $userName.$_POST['name'];
$sql="CREATE table $dataTableName(myItemName VARCHAR(30) NOT NULL, myItemLocation VARCHAR(30) NOT NULL);";
$db->exec($sql);
$sql = "INSERT INTO $myUserName (itemTableName, itemTableShort) VALUES (:name, :short)";
$stmt = $db->prepare($sql);
$stmt->execute(array(':name' => $dataTableName, ':short' => $dataTableShort));
$affected_rows = $stmt->rowCount();

$sql = "INSERT INTO $firstUserName (itemTableName, itemTableShort) VALUES (:name, :short)";
$stmt = $db->prepare($sql);
$stmt->execute(array(':name' => $dataTableName, ':short' => $dataTableShort));
$affected_rows = $stmt->rowCount();

$sql = "INSERT INTO $secondUserName (itemTableName, itemTableShort) VALUES (:name, :short)";
$stmt = $db->prepare($sql);
$stmt->execute(array(':name' => $dataTableName, ':short' => $dataTableShort));
$affected_rows = $stmt->rowCount();

$sql = "INSERT INTO $thirdUserName (itemTableName, itemTableShort) VALUES (:name, :short)";
$stmt = $db->prepare($sql);
$stmt->execute(array(':name' => $dataTableName, ':short' => $dataTableShort));
$affected_rows = $stmt->rowCount();


try {
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