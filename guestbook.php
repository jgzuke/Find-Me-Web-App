<head>
  <link type="text/css" rel="stylesheet" href="/css/main.css" />
</head>
<html>

<body>
  <?php
    use google\appengine\api\users\User;
    use google\appengine\api\users\UserService;

    $user = UserService::getCurrentUser();

    if ($user) {
      echo 'Hello, ' . htmlspecialchars($user->getNickname());
    }
    else {
      header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
    }
  ?>
  <h1 id="topname">Find Me</h1>
  <hr width="100%" background-color="#FFFFFF" size="4" height="2px"></hr>
  <div class="row">
    <div id="Find" class="col-md-6">
      <h2>Find</h2>
        <?php
          $db = null;
          if (isset($_SERVER['SERVER_SOFTWARE']) &&
            strpos($_SERVER['SERVER_SOFTWARE'], 'Google App Engine') !== false)
          {
            // Connect from App Engine.
            try
            {
              $db = new pdo('mysql:unix_socket=/cloudsql/findmewebapp:cloudinstanceid;dbname=guestbook', 'root', 'temppass');
            }
            catch (PDOException $ex)
            {
              die(json_encode(
                array('outcome' => false, 'message' => 'Unable to connect.')
              ));
            }
          }
          else
          {
            // Connect from a development environment.
            try
            {
              $db = new pdo('mysql:host=127.0.0.1:8889;dbname=guestbook', 'root', 'temppass');
            }
            catch (PDOException $ex)
            {
              die(json_encode(
                array('outcome' => false, 'message' => 'Unable to connect')
              ));
            }
          }
          try
          {
            // Show existing guestbook entries.
            foreach($db - > query('SELECT * from entries') as $row)
            {
              echo "<div><strong>".$row['name'].
              "</strong> wrote <br> ".$row['location'].
              "</div>";
            }
          }
          catch (PDOException $ex)
          {
            echo "An error occurred in reading or writing to guestbook.";
          }
          $db = null;
        ?>
      <div><strong>" . $row['name'] . "</strong> wrote
        <br> " . $row['location'] . "</div>"; } } catch (PDOException $ex) { echo "An error occurred in reading or writing to guestbook."; } $db = null; ?>
    </div>
    <div id="Move" class="col-md-6">
      <h2>Move</h2>
      <form action="/sign" method="post">
        <div>
          <textarea name="content" rows="3" cols="60"></textarea>
        </div>
        <div>
          <input type="submit" value="Sign Guestbook">
        </div>
      </form>
    </div>
  </div>
</body>

</html>