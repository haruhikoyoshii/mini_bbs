<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id'])){
  $id = $_REQUEST['id'];

  $messages = $db->prepare('select * from posts where id=?');
  $messages->execute(array($id));
  $message = $messages->fetch();

  if($message['member_id'] == $_SESSION['id']){
    $del = $db->prepare('delete from posts where id=?');
    $del->execute(array($id));

  }
}

header('location:index.php');
exit();
?>