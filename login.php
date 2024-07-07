<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

//login

if(isset($_POST['submitlogin'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
   if($select_user->rowCount() > 0){
     setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:home.php');
   }else{
      $message[] = 'incorrect email or password!';
   }

}

//Register

if(isset($_POST['submitregister'])){

$id = unique_id();
$name = $_POST['name'];
$name = filter_var($name, FILTER_SANITIZE_STRING);
$email = $_POST['email'];
$email = filter_var($email, FILTER_SANITIZE_STRING);
$pass = sha1($_POST['pass']);
$pass = filter_var($pass, FILTER_SANITIZE_STRING);
$cpass = sha1($_POST['cpass']);
$cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

$image = $_FILES['image']['name'];
$image = filter_var($image, FILTER_SANITIZE_STRING);
$ext = pathinfo($image, PATHINFO_EXTENSION);
$rename = unique_id().'.'.$ext;
$image_size = $_FILES['image']['size'];
$image_tmp_name = $_FILES['image']['tmp_name'];
$image_folder = 'uploaded_files/'.$rename;

$select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
$select_user->execute([$email]);

if($select_user->rowCount() > 0){
   $message[] = 'email already taken!';
}else{
   if($pass != $cpass){
      $message[] = 'confirm passowrd not matched!';
   }else{
      $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
      $insert_user->execute([$id, $name, $email, $cpass, $rename]);
      move_uploaded_file($image_tmp_name, $image_folder);
      
      $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
      $verify_user->execute([$email, $pass]);
      $row = $verify_user->fetch(PDO::FETCH_ASSOC);
      
      if($verify_user->rowCount() > 0){
         setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
         header('location:home.php');
      }
   }
}

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examgis | Login & Register</title>
    <!-- BOXICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- STYLE -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>


    <!-- Form Container -->
    <div class="form-container">
        <div class="col col-1">
            <div class="image-layer">
                <img src="assets/img/girl.png" class="form-image-main">
            </div>
            <p class="featured-words">Welcome to <span>Examgis</span></p>
        </div>

        <div class="col col-2">
            <div class="btn-box">
                <button class="btn btn-1" id="login">Sign in</button>
                <button class="btn btn-2" id="register">Sign up</button>
            </div>
            <!-- Login Form Container -->

            <form action="" method="post" enctype="multipart/form-data" class="login">
               <div class="login-form">
                  <div class="form-title">
                     <span>Sign In</span>
                  </div>
                  <div class="form-inputs">
                     <div class="input-box">
                           <input type="text" name="email" class="input-field" placeholder="Email" required>
                           <i class="bx bx-user icon"></i>
                     </div>
                     <div class="input-box">
                           <input type="password" name="pass" class="input-field" placeholder="Password" required>
                           <i class="bx bx-lock-alt icon"></i>
                     </div>
                     <div class="forgot-pass">
                           <a href="#">Forget Password?</a>
                     </div>
                     <div class="input-box">
                           <button class="input-submit" name="submitlogin">
                              <span>Sign In</span>
                              <i class="bx bx-right-arrow-alt"></i>
                           </button>
                     </div>
                  </div>
               </div>     
            </form>
                
            
            <!-- Register Form Container -->

            <form class="register" action="" method="post" enctype="multipart/form-data">
               <div class="register-form">
                  <div class="form-title">
                     <span>Create Account</span>
                  </div>
                  <div class="form-inputs">
                        <div class="input-box">
                              <input type="text" name="name" class="input-field" placeholder="Name" required>
                              <i class="bx bx-user icon"></i>
                     <div class="input-box">
                           <input type="email" name="email" class="input-field" placeholder="Email" required>
                           <i class="bx bx-envelope icon"></i>
                     </div>
                     <div class="input-box">
                           <input type="password" name="pass" class="input-field" placeholder="Password" required>
                           <i class="bx bx-lock-alt icon"></i>
                     </div>
                     <div class="input-box">
                           <input type="password" name="cpass" class="input-field" placeholder="Confirm Password" required>
                           <i class="bx bx-lock-alt icon"></i>
                     </div>
                     <div class="input-box">
                           <input type="file" name="image" accept="image/*" class="input-field" required>
                           <i class="bx bx-image-add icon"></i>
                     <div class="input-box">
                           <button class="input-submit" name="submitregister">
                              <span>Sign Up</span>
                              <i class="bx bx-right-arrow-alt"></i>
                           </button>
                     </div>
                  </div>        
               </div>
            </form>
        </div>
    </div>



    <!-- JS -->
    <script src="assets/js/main.js"></script>


   
</body>
</html>