Masters
=======

Mastersは、設定ファイルを書くだけで簡単にマスタ管理画面ができるPHP製のツールです。  
（内部的には[P3 Framework](http://code.google.com/p/p3-framework/), jQuery, Twitter Bootstrap, Bootswatch, Font Awesomeを使っています。）  
ライセンスは[MITライセンス](http://0-oo.net/pryn/MIT_license.txt)です。

-----

__基本的な使い方__
* 新規データの追加：一番下の行をダブルクリック。保存する時は右側のボタンをクリック
* 既存データの変更：変更したいデータのセルをダブルクリック。保存する時は右側のボタンをクリック
* 絞り込み検索：上部の＋ボタンをクリックし、検索条件を入力し、検索（虫眼鏡）ボタンをクリック

[動作サンプルはこちら](http://0-oo.net/masters/)です。

-----

__設定ファイルの作り方__
* 設定ファイルは"_conf.php"という名前で _conf.sample.phpと同じ場所に置いてください。  
* 設定の書き方は、_conf.sample.phpを参考にしてください。animalsのようにほとんど何も書かなくても動きますが、petsのように色々書くと使いやすくなります。

-----

__TODO__
* 日付型/日時型の入力時にDatepickerを表示する
* 外部キーは編集モードでなくてもKEYでなく値（名称等）を表示する
* ログイン
* 英語対応
