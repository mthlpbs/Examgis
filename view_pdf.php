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

if(isset($_POST['like_paper'])){

   if($user_id != ''){

      $paper_id = $_POST['paper_id'];
      $paper_id = filter_var($paper_id, FILTER_SANITIZE_STRING);

      $select_paper = $conn->prepare("SELECT * FROM `paper` WHERE id = ? LIMIT 1");
      $select_paper->execute([$paper_id]);
      $fetch_paper = $select_paper->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_paper['tutor_id'];

      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND paper_id = ?");
      $select_likes->execute([$user_id, $paper_id]);

      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND paper_id = ?");
         $remove_likes->execute([$user_id, $paper_id]);
         $message[] = 'Removed from likes!';
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, tutor_id, paper_id) VALUES(?,?,?)");
         $insert_likes->execute([$user_id, $tutor_id, $paper_id]);
         $message[] = 'Added to likes!';
      }

   }else{
      $message[] = 'Please login first!';
   }

}

if(isset($_POST['add_comment'])){

   if($user_id != ''){

      $id = unique_id();
      $comment_box = $_POST['comment_box'];
      $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
      $paper_id = $_POST['paper_id'];
      $paper_id = filter_var($paper_id, FILTER_SANITIZE_STRING);

      $select_paper = $conn->prepare("SELECT * FROM `paper` WHERE id = ? LIMIT 1");
      $select_paper->execute([$paper_id]);
      $fetch_paper = $select_paper->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_paper['tutor_id'];

      if($select_paper->rowCount() > 0){

         $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE paper_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
         $select_comment->execute([$paper_id, $user_id, $tutor_id, $comment_box]);

         if($select_comment->rowCount() > 0){
            $message[] = 'Comment is already added!';
         }else{
            $insert_comment = $conn->prepare("INSERT INTO `comments`(id, paper_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
            $insert_comment->execute([$id, $paper_id, $user_id, $tutor_id, $comment_box]);
            $message[] = 'New comments are added!';
         }

      }else{
         $message[] = 'Something went wrong!';
      }

   }else{
      $message[] = 'Please login first!';
   }

}

if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'Comments are deleted successfully!';
   }else{
      $message[] = 'Comments are already deleted!';
   }

}

if(isset($_POST['update_now'])){

   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ?");
   $verify_comment->execute([$update_id, $update_box]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'Comments are already added!';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = 'Comments are edited successfully!';
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
   <title>View Papers - ExamGIS</title>

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

<?php
   if(isset($_POST['edit_comment'])){
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
      $verify_comment->execute([$edit_id]);
      if($verify_comment->rowCount() > 0){
         $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="edit-comment">
   <h1 class="heading">Edit comment</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="Please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <a href="view_pdf.php?get_id=<?= $get_id; ?>" class="inline-option-btn">Cancel edit</a>
         <input type="submit" value="update now" name="update_now" class="inline-btn">
      </div>
   </form>
</section>
<?php
   }else{
      $message[] = 'Comment was not found!';
   }
}
?>

<!-- view pdf section starts  -->

<section class="view-pdf">

   <?php
      $select_paper = $conn->prepare("SELECT * FROM `paper` WHERE id = ? AND status = ?");
      $select_paper->execute([$get_id, 'active']);
      if($select_paper->rowCount() > 0){
         while($fetch_paper = $select_paper->fetch(PDO::FETCH_ASSOC)){
            $paper_id = $fetch_paper['id'];

            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE paper_id = ?");
            $select_likes->execute([$paper_id]);
            $total_likes = $select_likes->rowCount();  

            $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND paper_id = ?");
            $verify_likes->execute([$user_id, $paper_id]);

            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$fetch_paper['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   ?>
   <div class="pdf-details">
   <embed src="uploaded_files/papers/<?= urlencode($fetch_paper['pdf']); ?>" type="application/pdf" scrolling="auto" class="pdf" style="width:100%; height:500px;" frameborder="0"></embed>
      <h3 class="title"><?= $fetch_paper['title']; ?></h3>
      <div class="info">
         <p><i class="fas fa-calendar"></i><span><?= $fetch_paper['date']; ?></span></p>
         <p><i class="fas fa-heart"></i><span><?= $total_likes; ?> likes</span></p>
      </div>
      <div class="tutor">
         <img src="uploaded_files/tutor_thumb/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_tutor['profession']; ?></span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <input type="hidden" name="paper_id" value="<?= $paper_id; ?>">
         <a href="course_desc.php?get_id=<?= $fetch_paper['course_id']; ?>" class="inline-btn">view course</a>
         <?php
            if($verify_likes->rowCount() > 0){
         ?>
         <button type="submit" name="like_paper"><i class="fas fa-heart"></i><span>liked</span></button>
         <?php
         }else{
         ?>
         <button type="submit" name="like_paper"><i class="far fa-heart"></i><span>like</span></button>
         <?php
            }
         ?>
      </form>
      <div class="description"><p><?= $fetch_paper['description']; ?></p></div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No papers are added yet!</p>';
      }
   ?>

</section>

<!-- view pdf section ends -->

<!-- comments section starts  -->

<section class="comments">

   <h1 class="heading">Write your comment</h1>

   <form action="" method="post" class="add-comment">
      <input type="hidden" name="paper_id" value="<?= $get_id; ?>">
      <textarea name="comment_box" required placeholder="write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" name="add_comment" class="inline-btn">
   </form>

   <h1 class="heading">Comments</h1>

   
   <div class="show-comments">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE paper_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="user">
            <img src="uploaded_files/user_thumb/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">edit</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('Do you want to delete this comment?');">delete</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">No comments are added yet!</p>';
      }
      ?>
      </div>
   
</section>

<!-- comments section ends -->

<!-- custom js file link  --> 
<script src="js/script.js"></script>
   
</body>
</html>