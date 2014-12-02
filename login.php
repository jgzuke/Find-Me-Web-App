<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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
  <h1 id="topname">Login</h1>
  <hr width="100%"  background-color="#FFFFFF" size="4" height = "2px"></hr>
    <div class="row">
      <div id = "Find" class="col-md-6">
        <h2>Login</h2>
        <div id = "loginForm">
          <form action="" method="post">
            <input type="text" name="email" value="email"> 
            <input type="text" name="password" value="password">
            <input type="submit" value="Login" name = "login">
          </form>
        </div>
      </div>
      <div id = "Move" class="col-md-6">
        <h2>Sign up</h2>
        <div id = "signupForm">
          <form action="" method="post">
            <input type="text" name="email" value="email"> 
            <input type="text" name="password" value="password">
            <input type="text" name="password2" value="confirm">
            <input type="submit" value="Sign Up" name = "signup">
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