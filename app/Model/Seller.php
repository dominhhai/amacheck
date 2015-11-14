<?php

App::uses('AppModel', 'Model');

class Seller extends AppModel {

	public $validateAll = array(
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
		'price_min' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '最小金額は必須です。'
			),
			'naturalNumber' => array(
				'rule' => 'numeric',
				'message' => '数字を入力してください。'
			),
			'lower' => array(
				'rule' => 'isSmallerThan',
				'message' => '最高金額より小さい金額を入力してください。'
				)
		),
		'price_max' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '最高金額は必須です。'
			),
			'naturalNumber' => array(
				'rule' => 'numeric',
				'message' => '数字を入力してください。'
			)
		)
	);

	public $validatePrice = array(
		'price_min' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '最小金額は必須です。'
			),
			'naturalNumber' => array(
				'rule' => 'numeric',
				'message' => '数字を入力してください。'
			),
			'lower' => array(
				'rule' => 'isSmallerThan',
				'message' => '最高金額より小さい金額を入力してください。'
				)
		),
		'price_max' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '最高金額は必須です。'
			),
			'naturalNumber' => array(
				'rule' => 'numeric',
				'message' => '数字を入力してください。'
			)
		)
	);

	function isSmallerThan($data) {
		return $this->data['Seller']['price_min'] <= $this->data['Seller']['price_max'];
	}
}