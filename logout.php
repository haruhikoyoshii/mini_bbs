<?php 
session_start();
// セッションの削除
$_SESSION = array();
// クッキーの削除
if(ini_set('session.use_cookies')){
  $params = session_get_cookie_params();
  setcookie(session_name() . ' , time() - 42000',$params['path'],$params['domain'],$params['secure'],$params['httponly']);
}
session_destroy();

setcookie('email','','time()-3600');

header('location:login.php');
exit();
?>