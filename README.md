# mini_bbs
PHPの学習の為に制作したひとこと掲示板  
会員登録、ログイン、ログアウト、メッセージの投稿、削除、メッセージへの返信が行える。

__動作させるにはMySQLが必要になります。__

## 使い方
`git clone https://github.com/YosukeIMAI312/mini_bbs.git`

__データベースを作成する__  
`$ mysql -u root`     
`$ source /*setupDB.sqlファイルをドラック&ドロップ*/`  

__mini_bbsのディレクトリまで移動し、ビルトインサーバーを立ち上げる__  
`php -S localhost:8000`  

__以下のURLにアクセス__  
http://localhost:8000
