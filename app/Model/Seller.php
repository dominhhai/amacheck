<?php

App::uses('AppModel', 'Model');

class Seller extends AppModel {

	public $validate = array(
		'file' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '出品者CSVファイルは必須です。'
			),
			'uploadError' => array(
				'rule' => 'uploadError',
				'message' => 'ファイルアップロードで障害が起こりました。'
			)
		),
		'price' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '最高価格は必須です。'
			),
			'naturalNumber' => array(
				'rule' => 'naturalNumber',
				'message' => '数字を入力してください。'
			)
		)
	);
}