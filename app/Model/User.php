<?php

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

	public $validatePass  = array(
			'passwd' => array(
				'required' => array(
					'rule' => 'notBlank',
					'message' => '現在のパスワードは必須です。'
				),
				'minLength' => array(
					'rule' => array('minLength', 6),
					'message' => 'パスワードは６文字以上入力してください。'
				)
			),
			'password' => array(
				'required' => array(
					'rule' => 'notBlank',
					'message' => '新しいパスワードは必須です。'
				),
				'minLength' => array(
					'rule' => array('minLength', 6),
					'message' => 'パスワードは６文字以上入力してください。'
				)
			),
			'psword' => array(
				'required' => array(
					'rule' => 'notBlank',
					'message' => '新しいパスワードの確認は必須です。'
				),
				'minLength' => array(
					'rule' => array('minLength', 6),
					'message' => 'パスワードは６文字以上入力してください。'
				)
			),
		);

	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'ユーザ名は必須です。'
			),
			'alphanumeric' => array(
				'rule' => 'alphanumeric',
				'message' => 'ユーザ名には文字と数字だけしか使えません。'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'required' => 'create',
				'message' => 'そのユーザ名は既に使われています。'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => 'notBlank',
				'required' => 'create',
				'message' => 'パスワードは必須です。'
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'パスワードは６文字以上入力してください。'
			)
		),
		'name' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => '氏名は必須です。'
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('inList', array('0', '999')),
				'message' => 'ロールは必須です。',
				'allowEmpty' => false
			)
		)
	);

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}
}