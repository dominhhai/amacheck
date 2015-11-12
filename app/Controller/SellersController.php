<?php

App::uses('AppController', 'Controller');

class SellersController extends AppController {

	public $uses = array('Seller', 'Product', 'Price');

	public function index() {
		$this->set('title_for_layout', '商品一覧');
	}

	public function price() {
		$this->set('title_for_layout', 'ランキング変動');
	}

}