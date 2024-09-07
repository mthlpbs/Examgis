<?php

error_reporting(E_ALL); // Remove or comment out in production
ini_set('display_errors', 1); // Remove or comment out in production

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
    header('Location:index.php');
    exit();
}

//login

if(isset($_POST['submitlogin'])){
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Updated
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Updated

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
   if($select_user->rowCount() > 0){
     setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:index.php');
   }else{
      $message[] = 'incorrect email or password!';
   }

}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
       <!-- meta properties -->
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <title>Login to ExamGIS</title>

       <!-- Fav-icon -->
       <link rel="apple-touch-icon" sizes="180x180" href="./images/favicon/apple-touch-icon.png">
       <link rel="icon" type="image/png" sizes="32x32" href="./images/favicon/favicon-32x32.png">
       <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon/favicon-16x16.png">
       <link rel="manifest" href="./images/site.webmanifest">

       <!-- font awesome cdn link  -->
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

       <!-- custom css file link  -->
       <link rel="stylesheet" href="./css/login.css">

    </head>

    <body>
        <div class="container">
            <div class="header">
                <a href=""><img src="./images/favicon/apple-touch-icon.png" alt="ExamGIS_logo" width="70px" height="70px"><span></span></a>
                <h1>Sign in to ExamGIS</h1>
            </div>
            <form action="" method="post" enctype="multipart/form-data" >
                <div class="form-group">
                    <input type="email" name="email" id="email" required class="inputField" autocomplete="off">
                    <label for="email">Email</label>
                </div>
                <div class="form-group">
                    <input type="password" name="pass" id="password" required class="inputField" autocomplete="off">
                    <label for="password">Password</label>
                </div>
                <button type="submit" class="submitButton" name="submitlogin">Log in</button>
            </form>
            <div class="footer">
                <a href="./register.php">Sign up for ExamGIS</a>
            </div>
        </div>
        <footer class="footer-del">
            <span class="author">
                © 2024  MTC LLC. All rights reserved.
            </span>
        <footer>
    </body>
</html> 