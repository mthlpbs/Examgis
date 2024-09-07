<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){
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
   $message[] = 'course deleted!';
   }else{
      $message[] = 'course already deleted!';
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
   <title>Playlist | ExamGIS</title>
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

<section class="playlists">

   <h1 class="heading">Added a course</h1>

   <div class="box-container">
   
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">Add a new course</h3>
         <a href="add_playlist.php" class="btn">Add course</a>
      </div>

      <?php
         $select_course = $conn->prepare("SELECT * FROM `course` WHERE tutor_id = ? ORDER BY date DESC");
         $select_course->execute([$tutor_id]);
         if($select_course->rowCount() > 0){
         while($fetch_course = $select_course->fetch(PDO::FETCH_ASSOC)){
            $course_id = $fetch_course['id'];
            $count_papers = $conn->prepare("SELECT * FROM paper` WHERE course_id = ?");
            $count_papers->execute([$course_id]);
            $total_papers = $count_papers->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_course['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_course['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_course['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_course['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_papers; ?></span>
            <img src="../uploaded_files/course_thumb/<?= $fetch_course['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <p class="description"><?= $fetch_course['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="course_id" value="<?= $course_id; ?>">
            <a href="update_playlist.php?get_id=<?= $course_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this course?');" name="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $course_id; ?>" class="btn">view courses</a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">No course added yet!</p>';
      }
      ?>

   </div>

</section>


<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>