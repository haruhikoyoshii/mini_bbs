<?php 
session_start();
require('../dbconnect.php');

if(!empty($_POST)){
	if($_POST['name'] === ''){
		$error['name'] = 'blank';
	}
	if($_POST['email'] === ''){
		$error['email'] = 'blank';
	}
	if(strlen($_POST['password']) <4){
		$error['password'] = 'length';
	}
	if($_POST['password'] === ''){
		$error['password'] = 'blank';
	}
	$fileName = $_FILES['image']['name'];
	if(!empty($fileName)){
		//ファイルの拡張子を確認
		$ext = substr($fileName,-3);
		if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
			$error['image'] = 'type';
		}
	}

	// アカウントの重複チェック
	if(empty($error)){
		$member = $db->prepare('select count(*) as cnt from members where email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if($record['cnt'] > 0){
			$error['email'] = 'duplicate';
		}
	}

	// エラーがなければチェック画面へ
	if(empty($error)){
		$image = date('YmdHis') . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		header('location:check.php');
		exit();
	}
}
if(isset($_REQUEST['action'])){
	if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
		$_POST = $_SESSION['join'];
	}
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php if(isset($_POST['name']))print(h($_POST['name'],ENT_QUOTES)); ?>" />
					<?php if (isset($error['name']) && $error['name'] == 'blank'): ?>
					<p class="error">* 名前を入力してください</p>
					<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php if(isset($_POST['email']))print(h($_POST['email'],ENT_QUOTES)); ?>" />
					<?php if (isset($error['email']) && $error['email'] == 'blank'): ?>
					<p class="error">* メールアドレスを入力してください</p>
					<?php endif; ?>
					<?php if (isset($error['email']) && $error['email'] == 'duplicate'): ?>
					<p class="error">* 入力されたメールアドレスは既に使用されています</p>
					<?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="" />
					<?php if (isset($error['password']) && $error['password'] == 'length'): ?>
					<p class="error">* パスワードは4文字以上で入力してください</p>
					<?php endif; ?>
					<?php if (isset($error['password']) && $error['password'] == 'blank'): ?>
					<p class="error">*  パスワードを入力してください</p>
					<?php endif; ?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
					<?php if (isset($error['image']) && $error['image'] === 'type'): ?>
					<p class="error">* 「jpg」「png」「gif」の画像を指定してください</p>
					<?php endif; ?>
					<?php if(!empty($error)): ?>
						<p class="error">*  画像を改めて指定してください</p>
					<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
