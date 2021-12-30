<?php
$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];

require_once "application/config/constanta.php";

if ($hostProtocol == "180.211.92.131")
{
   $host = "http://mkp-operation.com:7793/".$url[1];
   header("Location: ".$host, true, 301);
}

?>


<!doctype html>
<html lang="en">
  <head>
  	<title>Login || Budget Application</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="assets/login/css/style.css">
    <link rel="icon" href="images/logomri.png" type="image/icon type">

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<?php

			if (ENVIRONMENT == "dev" || ENVIRONMENT == "uat") {
				echo '<div class="alert alert-danger" role="alert">
				<h4 class="alert-heading"><b>PERHATIAN!!!</b></h4>
				<p>APLIKASI INI BERJALAN PADA STATUS APLIKASI TAHAP <b>TESTING</b> DENGAN VERSI APLIKASI <i>BETA</i></p>
			</div>';
			}

			?>
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">BUDGET APPLICATION</h2>
					<?php
					if (ENVIRONMENT == "dev" || ENVIRONMENT == "uat") {
						echo '<img src="assets/login//beta-testing.png" height="80px"/>';
					}
					?>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-12 col-lg-10">
					<div class="wrap d-md-flex">
						<div class="img" style="background-image: url(images/logomri.png);">
			            </div>
						<div class="login-wrap p-4 p-md-5">
			      	<div class="d-flex">
			      		<div class="w-100">
			      			<h3 class="mb-4">Masuk Aplikasi</h3>
                            <?php
                            $isError = isset($_GET["error"]) ? $_GET["error"] : false;
                            if ($isError) {
                                echo '<div class="alert alert-danger" role="alert">
                                Username atau password tidak sesuai
                            </div>';
                            } 
                            ?>
			      		</div>
                          
			      	</div>
					<form action="ceklogin.php?op=in" method="POST" class="signin-form">
                        <div class="form-group mb-3">
                            <label class="label" for="name">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="label" for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary rounded submit px-3">Sign In</button>
                        </div>
		            </div>
		          </form>
		        </div>
		      </div>
			</div>
		</div>
	</section>

	<script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>

	</body>
</html>

