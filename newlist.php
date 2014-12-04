<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New List</title>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,600,700" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<html>
  <body>
    <h1 id="topname">New List</h1>
    <hr width="100%"  background-color="#FFFFFF" size="4" height = "2px"></hr>
    <div class = "myforms">
      <form action="/makenewlist" method="post">
        <div><input type="text" name="name" value="name"></div>
        <div><input type="text" name="email1" value="email1"></div>
        <div><input type="text" name="email2" value="email2"></div>
        <div><input type="text" name="email3" value="email3"></div>

        <div><input type="submit" value="Create" name = "Create">
        <input type="submit" value="Cancel" name = "Cancel"></div>
      </form>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js-image-slider.js" type="text/javascript"></script>
  </body>
</html>