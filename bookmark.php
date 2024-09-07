<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('Location: login.php');
   exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- meta properties -->
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Bookmarks - ExamGIS</title>

   <!-- Fav-icon -->
   <link rel="apple-touch-icon" sizes="180x180" href="./images/favicon/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="./images/favicon/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon/favicon-16x16.png">
   <link rel="manifest" href="./images/site.webmanifest">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="courses">

   <h1 class="heading">Bookmarked courses</h1>

   <div class="box-container">

      <?php
         $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
         $select_bookmark->execute([$user_id]);
         if($select_bookmark->rowCount() > 0){
            while($fetch_bookmark = $select_bookmark->fetch(PDO::FETCH_ASSOC)){
               $select_courses = $conn->prepare("SELECT * FROM `course` WHERE id = ? AND status = ? ORDER BY date DESC");
               $select_courses->execute([$fetch_bookmark['course_id'], 'active']);
               if($select_courses->rowCount() > 0){
                  while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){

                  $course_id = $fetch_course['id'];

                  $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                  $select_tutor->execute([$fetch_course['tutor_id']]);
                  $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/tutor_thumb/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/course_thumb/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="course_desc.php?get_id=<?= $course_id; ?>" class="inline-btn">View course</a>
      </div>
      <?php
               }
            }else{
               echo '<p class="empty">No courses are found!</p>';
            }
         }
      }else{
         echo '<p class="empty">Nothing bookmarked yet!</p>';
      }
      ?>

   </div>

</section>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
