<?php

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'ユーザ名は必須です。'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'パスワードは必須です。'
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
        if (!$this->id && isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }
}