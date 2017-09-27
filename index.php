<?php if (!isset($_SESSION)) {session_start();}
require_once 'php/_auth.php';/*if(!isset($_SESSION)) {sessionStart('_auth');}*/session_automatic_login(); ?>

<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Login - WSDT</title>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  <script type="text/javascript" src="./js/lightbox.js"></script>
  <link href="./css/lightbox.css" rel="stylesheet"/>
  <script type="text/javascript" src="./js/notify.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>

  <?php 
  $hasWSDTaPwd = true;
  $hasWSDTaPwd = db_hasWSDTaPwd('super_user');

  (!$hasWSDTaPwd) ? $set_super_user = "value='super_user' disabled" : $set_super_user = "";
  ?>

  <link rel="shortcut icon" type="image/x-icon" href="./img/login_icon.ico"/>
  
  <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css'/>
  <link rel='stylesheet' href='css/standard.css'/>

      <link rel="stylesheet" href="./css/login.css">

  
</head>

<body>
<?php 
echo "<script type='text/javascript'>";

if(!empty($_GET)) {
  if(isset($_GET['mustlogin'])) {
    echo "$.notify('Du musst dich vorher einloggen!','info');";
  }
  if(isset($_GET['loggedout'])) {
    echo "$.notify('Du wurdest ausgeloggt!','success');";
  }
  if(isset($_GET['timeout'])) {
     echo "$.notify('Aus Sicherheitsgründen, durch deine lange Inaktivität, ausgeloggt!','warn');";
  }
} else {
    echo "$.notify('Willkommen bei WSDT-Küche, dem Küchenverwaltungssystem!','info');";
}
echo "</script>";

?>


  <div class="login-form">
     <h1>WSDT</h1>
     <div class="form-group "><!-- DO NOT CHANGE id='UserName' to 'username'! 'username' is used by username field in reset_password! -->
       <input type="text" class="form-control" placeholder="Username" id="UserName" <?php echo $set_super_user;?>>
       <i class="fa fa-user"></i>
     </div>
     <div class="form-group log-status">
       <input type="password" class="form-control" placeholder="Password" id="Password">
       <i class="fa fa-lock"></i>
     </div>
     <?php 
     if ($hasWSDTaPwd===false) {
     echo '<div class="form-group log-status">
       <input type="password" class="form-control" placeholder="Repeat Password" id="Password_Repeat" name="set_password">
       <i class="fa fa-lock"></i>
     </div>';
    } else {
      echo '<span class="alert">Invalid Credentials</span>
      <a class="link" style="float:left;" id="register_fake_link" href="#" onclick="fake_register()" title="You have no account?">Register now</a>
      <a class="link" href="#" onclick="lost_password()" title="No problem! Just click on me!">Lost your password?</a>';
      } ?>
     <button type="button" class="log-btn">
     <?php
     echo ($hasWSDTaPwd===false) ? "Register" : "Log In";
     ?>
     </button>

    
   </div>
   <!-- Place an Url with JS here and the file will get downloaded if it is not processable)-->
    <iframe id="download_document" style="display:none;"></iframe>
    <script src="./js/login.js"></script>

</body>
</html>
<?php exit(); ?>
