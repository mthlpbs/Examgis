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
   header('location:dashboard.php');
}

if(isset($_POST['update'])){

   $pdf_id = $_POST['pdf_id'];
   $pdf_id = filter_var($pdf_id, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $course = $_POST['course'];
   $course = filter_var($pcours, FILTER_SANITIZE_STRING);

   $update_paper = $conn->prepare("UPDATE `paper` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_paper->execute([$title, $description, $status, $pdf_id]);

   if(!empty($course)){
      $update_course = $conn->prepare("UPDATE `paper` SET course_id = ? WHERE id = ?");
      $update_course->execute([$course, $pdf_id]);
   }

   $old_thumb = $_POST['old_thumb'];
   $old_thumb = filter_var($old_thumb, FILTER_SANITIZE_STRING);
   $thumb = $_FILES['thumb']['name'];
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = '../uploaded_files/paper_thumb/'.$rename_thumb;

   if(!empty($thumb)){
      if($thumb_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_thumb = $conn->prepare("UPDATE `paper` SET thumb = ? WHERE id = ?");
         $update_thumb->execute([$rename_thumb, $pdf_id]);
         move_uploaded_file($thumb_tmp_name, $thumb_folder);
         if($old_thumb != '' AND $old_thumb != $rename_thumb){
            unlink('../uploaded_files/paper_thumb/'.$old_thumb);
         }
      }
   }

   $old_pdf = $_POST['old_pdf'];
   $old_pdf = filter_var($old_pdf, FILTER_SANITIZE_STRING);
   $pdf = $_FILES['pdf']['name'];
   $pdf = filter_var($pdf, FILTER_SANITIZE_STRING);
   $pdf_ext = pathinfo($pdf, PATHINFO_EXTENSION);
   $rename_pdf = unique_id().'.'.$pdf_ext;
   $pdf_tmp_name = $_FILES['pdf']['tmp_name'];
   $pdf_folder = '../uploaded_files/papers/'.$rename_pdf;

   if(!empty($pdf)){
      $update_pdfo = $conn->prepare("UPDATE `paper` SET pdf = ? WHERE id = ?");
      $update_pdf->execute([$rename_pdf, $pdf_id]);
      move_uploaded_file($pdf_tmp_name, $pdf_folder);
      if($old_pdf != '' AND $old_pdf != $rename_pdf){
         unlink('../uploaded_files/papers/'.$old_pdf);
      }
   }

   $message[] = 'paper updated!';

}

if(isset($_POST['delete_pdf'])){

   $delete_id = $_POST['pdf_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $delete_pdf_thumb = $conn->prepare("SELECT thumb FROM `paper` WHERE id = ? LIMIT 1");
   $delete_pdf_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/paper_thumb/'.$fetch_thumb['thumb']);

   $delete_pdf = $conn->prepare("SELECT pdf FROM `paper` WHERE id = ? LIMIT 1");
   $delete_pdf->execute([$delete_id]);
   $fetch_pdf = $delete_pdf->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/paper_thumb/'.$fetch_pdf['pdf']);

   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE paper_id = ?");
   $delete_likes->execute([$delete_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE paper_id = ?");
   $delete_comments->execute([$delete_id]);

   $delete_paper = $conn->prepare("DELETE FROM `paper` WHERE id = ?");
   $delete_paper->execute([$delete_id]);
   header('location:contents.php');
    
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
   <title>Update Papers | ExamGIS</title>
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
   
<section class="video-form">

   <h1 class="heading">update Paper</h1>

   <?php
      $select_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE id = ? AND tutor_id = ?");
      $select_pdfs->execute([$get_id, $tutor_id]);
      if($select_pdfs->rowCount() > 0){
         while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
            $pdf_id = $fecth_pdfs['id'];
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="pdf_id" value="<?= $fecth_pdfs['id']; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fecth_pdfs['thumb']; ?>">
      <input type="hidden" name="old_pdf" value="<?= $fecth_pdfs['pdf']; ?>">
      <p>update status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fecth_pdfs['status']; ?>" selected><?= $fecth_pdfs['status']; ?></option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>update title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter pdf title" class="box" value="<?= $fecth_pdfs['title']; ?>">
      <p>update description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"><?= $fecth_pdfs['description']; ?></textarea>
      <p>update course</p>
      <select name="course" class="box">
         <option value="<?= $fecth_pdfs['course_id']; ?>" selected>--select couerse</option>
         <?php
         $select_courses = $conn->prepare("SELECT * FROM `course` WHERE tutor_id = ?");
         $select_courses->execute([$tutor_id]);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_course['id']; ?>"><?= $fetch_course['title']; ?></option>
         <?php
            }
         ?>
         <?php
         }else{
            echo '<option value="" disabled>no course created yet!</option>';
         }
         ?>
      </select>
      <img src="../uploaded_files/paper_thumb/<?= $fecth_pdfs['thumb']; ?>" alt="">
      <p>update Cover</p>
      <input type="file" name="thumb" accept="image/*" class="box">
      <video src="../uploaded_files/paper_thumb/<?= $fecth_pdfs['pdf']; ?>" controls></video>
      <p>update Paper</p>
      <input type="file" name="pdf" accept="pdf/*" class="box">
      <input type="submit" value="update paper" name="update" class="btn">
      <div class="flex-btn">
         <a href="view_content.php?get_id=<?= $pdf_id; ?>" class="option-btn">view Paper</a>
         <input type="submit" value="delete paper" name="delete_pdf" class="delete-btn">
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">The paper not found! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add paper</a></p>';
      }
   ?>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>