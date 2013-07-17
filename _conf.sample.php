<?php
// 基本情報
define('SITE_TITLE', 'マスタ管理');
define('BASE_URL', '/masters');	// https://www.example.com/masters の場合
define('DEBUG_FLG', true);

// DB接続
define('DB_NAME', 'masters');
define('DB_USER', 'masters');
define('DB_PASS', 'password');
define('DB_OTHER', '');
define('DB_CREATED_AT', 'created_at');	// INSERT日時を登録する全テーブル共通の列名
define('DB_UPDATED_AT', 'updated_at');	// UPDATE日時を登録する全テーブル共通の列名

// テーブルについての共通設定
$rules = array(
	'where' => array('delete_flg' => 0),	// 全テーブル共通の取得条件
	'order' => array('id'),	// 全テーブル共通の並び順
);

// 対象テーブル
$tables = array(
	'pets' => array(	// テーブルごとの設定
		'label' => 'ペット',	// テーブルの表示名
		'where' => array('name NOT LIKE' => '%丸'),	// 取得条件（共通設定を上書き）
		'order' => array('birth'),	// 並び順（共通設定を上書き）
		'columns' => array(	// 表示する列
			'id',
			'name' => '名前',	// 列の表示名を指定する場合
			'length' => '体長',
			'birth' => '誕生日',
			'delete_flg' => array(
				'label' => '削除フラグ',
				'select' => array(0, 1),	// 入力時の選択肢
			),
			'gender' => array(
				'label' => '性別',
				'select_value' => array(	// 入力時の選択肢（値を表示名が違う場合）
					'M' => 'オス',
					'F' => 'メス',
				),
			),
			'animal_id' => array(
				'label' => '動物ID',
				'foreign' => array(	// 列が外部キーの場合
					'table' => 'animals',
					'value' => 'id',	// 外部キーと紐づく列
					'label' => 'name',	// 入力時に表示させる列
					'where' => array('delete_flg' => 0),
					'order' => array('id'),
				),
			),
		),
	),
	
	'animals' => array(),	// デフォルト設定（と、あれば共通設定）のみ適用される
);
