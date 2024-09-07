<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:course.php');
}

if(isset($_POST['delete_course'])){
   $delete_id = $_POST['course_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $delete_course_thumb = $conn->prepare("SELECT * FROM `course` WHERE id = ? LIMIT 1");
   $delete_course_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_course_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/course_thumb/'.$fetch_thumb['thumb']);
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE course_id = ?");
   $delete_bookmark->execute([$delete_id]);
   $delete_course = $conn->prepare("DELETE FROM `course` WHERE id = ?");
   $delete_course->execute([$delete_id]);
   header('location:courses.php');
}

if(isset($_POST['delete_pdf'])){
   $delete_id = $_POST['pdf_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $verify_pdf = $conn->prepare("SELECT * FROM `paper` WHERE id = ? LIMIT 1");
   $verify_pdf->execute([$delete_id]);
   if($verify_pdf->rowCount() > 0){
      $delete_pdf_thumb = $conn->prepare("SELECT * FROM `paper` WHERE id = ? LIMIT 1");
      $delete_pdf_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/paper_thumb/'.$fetch_thumb['thumb']);
      $delete_pdf = $conn->prepare("SELECT * FROM `paper` WHERE id = ? LIMIT 1");
      $delete_pdf->execute([$delete_id]);
      $fetch_pdf = $delete_pdf->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/papers/'.$fetch_pdf['pdf']);
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE paper_id = ?");
      $delete_likes->execute([$delete_id]);
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE paper_id = ?");
      $delete_comments->execute([$delete_id]);
      $delete_paper = $conn->prepare("DELETE FROM `paper` WHERE id = ?");
      $delete_paper->execute([$delete_id]);
      $message[] = 'The paper is deleted!';
   }else{
      $message[] = 'The paper is already deleted!';
   }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- primary meta data-->
   <meta http-equiv="Content-Type" charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="keywords" content="Login,Log in,Signin,Sign in" lang="en">
   <meta name="title" content="ExamGIS: Your Ultimate Study Companion">
   <meta name="description" content=" ExamGIS is a user-friendly platform that provides essential study materials for Saegis students. Whether you need resources, textbooks, or past papers, ExamGIS has you covered. It’s designed to support your academic journey and help you excel in your studies">
   <meta name="language" content="English">
   <meta name="author" content="TCM inc">
   <meta name="owner" content="-">

   <!-- meta properties -->
   <title>Category Details - ExamGIS</title>
   <meta name="title" content="ExamGIS: Your Ultimate Study Companion" />
   <meta name="description" content="ExamGIS is a comprehensive platform designed to support students at Saegis Campus. Whether you’re looking for resources, textbooks, or past papers, ExamGIS has you covered. Our user-friendly interface provides easy access to essential study materials, helping you excel in your academic journey." />
   <meta property="og:type" content="website" />
   <meta property="og:url" content="https://examgis.rf.gd" />
   <meta property="og:title" content="ExamGIS: Your Ultimate Study Companion" />
   <meta property="og:description" content="ExamGIS is a comprehensive platform designed to support students at Saegis Campus. Whether you’re looking for resources, textbooks, or past papers, ExamGIS has you covered. Our user-friendly interface provides easy access to essential study materials, helping you excel in your academic journey." />
   <meta property="og:image" content="https://i.imgur.com/WVc46oI.jpeg" />
   <meta property="twitter:card" content="summary_large_image" />
   <meta property="twitter:url" content="https://examgis.rf.gd" />
   <meta property="twitter:title" content="ExamGIS: Your Ultimate Study Companion" />
   <meta property="twitter:description" content="ExamGIS is a comprehensive platform designed to support students at Saegis Campus. Whether you’re looking for resources, textbooks, or past papers, ExamGIS has you covered. Our user-friendly interface provides easy access to essential study materials, helping you excel in your academic journey." />
   <meta property="twitter:image" content="https://i.imgur.com/WVc46oI.jpeg" />

   <!-- Fav-icon -->
   <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="course-details">

   <h1 class="heading">Course details</h1>

   <?php
      $select_course = $conn->prepare("SELECT * FROM `course` WHERE id = ? AND tutor_id = ?");
      $select_course->execute([$get_id, $tutor_id]);
      if($select_course->rowCount() > 0){
         while($fetch_course = $select_course->fetch(PDO::FETCH_ASSOC)){
            $course_id = $fetch_course['id'];
            $count_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE course_id = ?");
            $count_pdfs->execute([$course_id]);
            $total_pdfs = $count_pdfs->rowCount();
   ?>
   <div class="row">
      <div class="thumb">
         <span><?= $total_pdfs; ?></span>
         <img src="../uploaded_files/course_thumb/<?= $fetch_course['thumb']; ?>" alt="">
      </div>
      <div class="details">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_course['date']; ?></span></div>
         <div class="description"><?= $fetch_course['description']; ?></div>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="course_id" value="<?= $course_id; ?>">
            <a href="update_course.php?get_id=<?= $course_id; ?>" class="option-btn">update course</a>
            <input type="submit" value="delete course" class="delete-btn" onclick="return confirm('delete this course?');" name="delete">
         </form>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no course found!</p>';
      }
   ?>

</section>

<section class="contents">

   <h1 class="heading"> Courses</h1>

   <div class="box-container">

   <?php
      $select_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE tutor_id = ? AND course_id = ?");
      $select_pdfs->execute([$tutor_id, $course_id]);
      if($select_pdfs->rowCount() > 0){
         while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
            $pdf_id = $fecth_pdfs['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_pdfs['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_pdfs['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/paper_thumb/<?= $fecth_pdfs['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_pdfs['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <a href="update_paper.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this paper?');" name="delete_pdf">
         </form>
         <a href="view_paper.php?get_id=<?= $pdf_id; ?>" class="btn">Open Paper</a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no papers added yet! <a href="add_paper.php" class="btn" style="margin-top: 1.5rem;">add papers</a></p>';
      }
   ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>