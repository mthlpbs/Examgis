<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
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
      unlink('../uploaded_files/paper_thumb/'.$fetch_pdf['pdf']);
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

if(isset($_POST['delete_course'])){
   $delete_id = $_POST['course_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_course = $conn->prepare("SELECT * FROM `course` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_course->execute([$delete_id, $tutor_id]);

   if($verify_course->rowCount() > 0){

   $delete_course_thumb = $conn->prepare("SELECT * FROM `course` WHERE id = ? LIMIT 1");
   $delete_course_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_course_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/course_thumb/'.$fetch_thumb['thumb']);
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE course_id = ?");
   $delete_bookmark->execute([$delete_id]);
   $delete_course = $conn->prepare("DELETE FROM `course` WHERE id = ?");
   $delete_course->execute([$delete_id]);
   $message[] = 'The course is deleted!';
   }else{
      $message[] = 'The course is already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
<section class="contents">
   <h1 class="heading">papers</h1>
   <div class="box-container">

   <?php
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
      $search = $_POST['search'];
      $select_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
      $select_pdfs->execute([$tutor_id]);
      if($select_pdfs->rowCount() > 0){
         while($fecth_pdfs = $select_pdfs->fetch(PDO::FETCH_ASSOC)){ 
            $pdf_id = $fecth_pdfs['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_pdfs['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_pdfs['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_pdfs['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/paper_thumb/<?= $fecth_pdf['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_pdfs['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
            <a href="update_paper.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('Delete this paper?');" name="delete_pdf">
         </form>
         <a href="view_paper.php?get_id=<?= $pdf_id; ?>" class="btn">view paper</a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no papers founds!</p>';
      }
   }else{
      echo '<p class="empty">please search something!</p>';
   }
   ?>

   </div>

</section>

<section class="courses">
   <h1 class="heading">Courses</h1>
   <div class="box-container">
   
      <?php
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         $select_course = $conn->prepare("SELECT * FROM `course` WHERE title LIKE '%{$search}%' AND tutor_id = ? ORDER BY date DESC");
         $select_course->execute([$tutor_id]);
         if($select_course->rowCount() > 0){
         while($fetch_course = $select_course->fetch(PDO::FETCH_ASSOC)){
            $course_id = $fetch_course['id'];
            $count_pdfs = $conn->prepare("SELECT * FROM `paper` WHERE course_id = ?");
            $count_pdfs->execute([$course_id]);
            $total_pdfs = $count_pdfs->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_course['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_course['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_course['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_course['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_pdfs; ?></span>
            <img src="../uploaded_files/course_thumb/<?= $fetch_course['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <p class="description"><?= $fetch_course['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="course_id" value="<?= $course_id; ?>">
            <a href="update_course.php?get_id=<?= $course_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete_course" class="delete-btn" onclick="return confirm('Delete this course?');" name="delete">
         </form>
         <a href="view_course.php?get_id=<?= $course_id; ?>" class="btn">view course</a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">no courses found!</p>';
      }}else{
         echo '<p class="empty">please search something!</p>';
      }
      ?>

   </div>
</section>
<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.courses .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>