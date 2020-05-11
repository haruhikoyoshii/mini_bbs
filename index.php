<?php
session_start();
require('dbconnect.php');

  if(isset($_SESSION['id']) && $_SESSION['time'] + 3600> time()){
    $_SESSION['time']=time();

    $members= $db->prepare('select * from members where id=?');
    $members->execute(array($_SESSION['id']));
    $member= $members->fetch();
  }else{
    header('location:login.php');
    exit();
  }

  if(!empty($_POST)){
    if($_POST['message']!==''){
      $message=$db->prepare('insert into posts set member_id=?, message=?,reply_message_id=?, created=NOW()');
      $message->execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id']
      ));
      header('location:index.php');
      exit();
    }
  }
  // ページネーション
  if(empty($_REQUEST['page'])){
    $_REQUEST['page'] = 1;
  }
  $page = $_REQUEST['page'];
  $page = max($page,1);

  
  // ユーザーの入力をそのまま使うわけではないのでqueryを使う
  $counts = $db->query('select count(*) as cnt from posts');
  $cnt = $counts->fetch();
  $maxPage=ceil($cnt['cnt']/5);
  $page = min($page,$maxPage);

  $start = ($page- 1) * 5;

  // membersにはm postsにはpのショートカットを設定している
  $posts = $db->prepare('select  m.name, m.picture, p.* from members m, posts p where m.id=p.member_id order by p. created desc limit ?,5');
  // executeのパラメータとして指定すると文字列で入るので数字で入れるためにbindParamを使う
  $posts->bindParam(1,$start,PDO::PARAM_INT);
  $posts->execute();

  if(isset($_REQUEST['res'])){
    // 返信の処理
    $response = $db->prepare('select m.name, m.picture, p.* from members m, posts p where m.id=p.member_id and p.id=?');
    $response->execute(array($_REQUEST['res']));
    $table = $response->fetch();
    $message= '@' . $table['name'] . ' ' . $table['message'];

  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php print(h($member['name'],ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"><?php if(isset($message))print(h($message,ENT_QUOTES)); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print(h($_REQUEST['res'],ENT_QUOTES)); ?>" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

  <?php foreach($posts as $post): ?>
    <div class="msg">
      <img src="member_picture/<?php print(h($post['picture'],ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(h($post['name'],ENT_QUOTES)); ?>" />
    <p><?php print(h($post['message'],ENT_QUOTES)); ?><span class="name">（<?php print(h($post['name'],ENT_QUOTES)); ?>）</span>[<a href="index.php?res=<?php print(h($post['id'],ENT_QUOTES)); ?>">Re</a>]</p>
    <p class="day"><a href="view.php?id=<?php print(h($post['id'],ENT_QUOTES)); ?>"><?php print(h($post['created'],ENT_QUOTES)); ?></a>
    <?php if($post['reply_message_id'] > 0): ?>
<a href="view.php?id=<?php print(h($post['reply_message_id'],ENT_QUOTES)); ?>">
返信元のメッセージ</a>
    <?php endif; ?>

  <?php if($_SESSION['id']== $post['member_id']):?>
[<a href="delete.php?id=<?php print(h($post['id'],ENT_QUOTES)); ?>"
style="color: #F33;">削除</a>]
  <?php endif; ?>
    </p>
    </div>
  <?php endforeach; ?>

<ul class="paging">
<?php if($page>1): ?>
<li><a href="index.php?page=<?php print($page-1); ?>">前のページへ</a></li>
  <?php endif; ?>
<?php if($page <$maxPage): ?>
<li><a href="index.php?page=<?php print($page+1); ?>">次のページへ</a></li>
<?php endif; ?>
</ul>
  </div>
</div>
</body>
</html>
