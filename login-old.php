<!DOCTYPE HTML>
<html>

<head>
  <title>Login || MRI Budget Application</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="keywords" content="Modern Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
  <script type="application/x-javascript">
    addEventListener("load", function() {
      setTimeout(hideURLbar, 0);
    }, false);

    function hideURLbar() {
      window.scrollTo(0, 1);
    }
  </script>
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <!-- Bootstrap Core CSS -->
  <link href="bootstrap/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
  <!-- Custom CSS -->
  <link href="bootstrap/css/style.css" rel='stylesheet' type='text/css' />
  <link href="css/font-awesome.css" rel="stylesheet">
  <!-- jQuery -->
  <script src="js/jquery.min.js"></script>
  <!----webfonts--->
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
  <!---//webfonts--->
  <!-- Bootstrap Core JavaScript -->
  <script src="js/bootstrap.min.js"></script>
</head>

<body id="login">
  <div class="login-logo">
    <a href="login.php">
      <h1>Budget Application</h1>
    </a>
  </div>
  <h2 class="form-heading">login</h2>
  <div class="app-cam">
    <form action="ceklogin.php?op=in" method="POST">
      <input type="text" class="text" name="id_user" placeholder="username" required>

      <input type="password" name="password" placeholder="password" required>

      <div class="submit"><input type="submit" onclick="myFunction()" name="submit" value="Login"></div>

    </form>
  </div>
  <div class="copy_layout login">
    <p>Copyright &copy; 2018 MRI Budget - Ing</p>
  </div>
</body>

</html>

