<?php

   include 'connect.php';

   setcookie('user_id', '', time() - 1, '/');

   header('Location: ../login.php');
   exit();

?>
