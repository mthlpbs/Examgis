<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('Location: login.php');
   exit();
}
 
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

if(isset($_POST['save_list'])){

   if($user_id != ''){
      
      $list_id = $_POST['list_id'];
      $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

      $select_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND course_id = ?");
      $select_list->execute([$user_id, $list_id]);

      if($select_list->rowCount() > 0){
         $remove_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND course_id = ?");
         $remove_bookmark->execute([$user_id, $list_id]);
         $message[] = 'Course is removed!';
      }else{
         $insert_bookmark = $conn->prepare("INSERT INTO `bookmark`(user_id, course_id) VALUES(?,?)");
         $insert_bookmark->execute([$user_id, $list_id]);
         $message[] = 'Course added!';
      }

   }else{
      $message[] = 'Please login first!';
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
   <title>Papers - ExamGIS</title>

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

<!-- Course section starts  -->

<section class="playlist">

   <h1 class="heading">Course details</h1>

   <div class="row">
 
      <?php
         $select_course = $conn->prepare("SELECT * FROM `course` WHERE id = ? and status = ? LIMIT 1");
         $select_course->execute([$get_id, 'active']);
         if($select_course->rowCount() > 0){
            $fetch_course = $select_course->fetch(PDO::FETCH_ASSOC);

            $course_id = $fetch_course['id'];

            $count_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE course_id = ?");
            $count_pdfs->execute([$course_id]);
            $total_pdfs = $count_pdfs->rowCount();

            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$fetch_course['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

            $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND course_id = ?");
            $select_bookmark->execute([$user_id, $course_id]);

      ?>

      <div class="col">
         <form action="" method="post" class="save-list">
            <input type="hidden" name="list_id" value="<?= $course_id; ?>">
            <?php
               if($select_bookmark->rowCount() > 0){
            ?>
            <button type="submit" name="save_list"><i class="fas fa-bookmark"></i><span>saved</span></button>
            <?php
               }else{
            ?>
               <button type="submit" name="save_list"><i class="far fa-bookmark"></i><span>save course</span></button>
            <?php
               }
            ?>
         </form>
         <div class="thumb">
            <span><?= $total_pdfs; ?>Papers</span>
            <img src="uploaded_files/course_thumb/<?= $fetch_course['thumb']; ?>" alt="">
         </div>
      </div>

      <div class="col">
         <div class="tutor">
            <img src="uploaded_files/tutor_thumb/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_tutor['profession']; ?></span>
            </div>
         </div>
         <div class="details">
            <h3><?= $fetch_course['title']; ?></h3>
            <p><?= $fetch_course['description']; ?></p>
            <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_course['date']; ?></span></div>
         </div>
      </div>

      <?php
         }else{
            echo '<p class="empty">This course was not found!</p>';
         }  
      ?>

   </div>

</section>

<!-- course section ends -->

<!-- PDFs container section starts  -->

<section class="pdf-container">

   <h1 class="heading">Papers</h1>

   <div class="box-container">

      <?php
         $select_paper = $conn->prepare("SELECT * FROM `paper` WHERE course_id = ? AND status = ? ORDER BY date DESC");
         $select_paper->execute([$get_id, 'active']);
         if($select_paper->rowCount() > 0){
            while($fetch_paper = $select_paper->fetch(PDO::FETCH_ASSOC)){  
      ?>
      <a href="view_pdf.php?get_id=<?= $fetch_paper['id']; ?>" class="box">
         <i class="fas fa-view"></i>
         <img src="uploaded_files/paper_thumb/<?= $fetch_paper['thumb']; ?>" alt="">
         <h3><?= $fetch_paper['title']; ?></h3>
      </a>
      <?php
            }
         }else{
            echo '<p class="empty">No paper are added yet!</p>';
         }
      ?>

   </div>

</section>

<!-- pdfs container section ends -->


<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>