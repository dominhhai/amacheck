<?php

App::uses('AppModel', 'Model');

class Product extends AppModel {
	public $belongsTo = array(
		'Seller' => array(
			'className' => 'Seller',
			'foreignKey' => 'seller_id'
		)
	);

	public $hasOneData = array(
		'Productinfo' => array(
			'className' => 'Productinfo',
			'foreignKey' => 'id'
		)
	);
}