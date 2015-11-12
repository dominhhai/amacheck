<?php

App::uses('AppModel', 'Model');

class Product extends AppModel {
	public $validate = array(
			'price' => array(
				'required' => array(
						'rule' => 'notBlank',
						'message' => '価格は必須です。'
					)
				),
			'seller' => array(
				'required' => array(
						'rule' => 'notBlank',
						'message' => 'セラーリストは必須です。'
					)
				)
		);
}