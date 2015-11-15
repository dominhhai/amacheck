<?php

App::uses('AppController', 'Controller');

class SellersController extends AppController {

	public $uses = array('Seller', 'Product', 'Price');
	public $components = array('Paginator');
	public $paginate = array(
		'limit'=> 10,
		'order'=> array(
			'Product.seller_id'=> 'asc'
			)
		);

	public function index() {
		$this->set('title_for_layout', '商品一覧');
		if ($this->request->is('post')) {
			// バリデーションを行う。
			if ($this->Seller->validates($this->request->data)) {
				$data = $this->request->data['Seller'];
				if (isset($data['file']['error']) && $data['file']['error'] == 0) {
					$this->Session->delete('sellers');
					// ファイル形式を確認。
					$mimetypes = array(
						'text/csv',
						'application/csv',
						'text/comma-separated-values',
						'application/excel',
						'application/vnd.ms-excel',
						'application/vnd.msexcel'
						);
					if (!in_array($data['file']['type'], $mimetypes)) {
						unlink($data['file']['tmp_name']);
						$this->Session->setFlash(__('CSVファイルを使ってください。'), 'default', array(), 'upload');
					} // アップロードできた場合、ファイルを読み込む。
					else {
						$sellers = $this->readSellerCsv($data['file']['tmp_name']);
						if ($sellers == false) { // 読み込みが失敗した。
							$this->Session->setFlash(
								__('ファイルの読み取りは失敗しました。も一度アップロードしてください。'),
								'default', array(), 'upload');
						} else {
							//　セッションにて保管する。
							$this->Session->write('sellers',
								array('price'=> array('min'=> $data['price_min'], 'max'=> $data['price_max']),
										'sellers'=> $sellers));
						}
						unlink($data['file']['tmp_name']);
					}
				} else if ($this->Session->read('sellers') == null) {
					$this->Session->setFlash(__('出品者ファイルをアップロードしてください。'), 'default', array(), 'upload');
				} else {
					$sellers = $this->Session->read('sellers');
					$sellers['price'] = array('min'=> $data['price_min'], 'max'=> $data['price_max']);
					$this->Session->write('sellers', $sellers);
				}
			} else {
				pr ($this->Seller->invalidFields());
			}
		}
		// 出品者一覧を読み込み
		if (($sellers = $this->Session->read('sellers')) != null) {
			$this->Seller->validate = $this->Seller->validatePrice;

			$this->set('sellers', $sellers['sellers']);
			$this->request->data['Seller']['price_min'] = $sellers['price']['min'];
			$this->request->data['Seller']['price_max'] = $sellers['price']['max'];
		} else {
			$this->Seller->validate = $this->Seller->validateAll;

			if ($this->request->is('get')) {
				$this->request->data['Seller']['price_min'] = 0;
				$this->request->data['Seller']['price_max'] = 1000;
			}
		}
		//　ステータスマスタ
		$this->set('status', array('未取得', '取得中', '取得済み', '取得失敗'));
	}

	public function price() {
		if (($sellers = $this->Session->read('sellers')) == null) {
			return $this->redirect(array('action'=> 'index'));
		}

		$this->set('title_for_layout', 'ランキング変動');
		$this->set('load_graph', true);
		$this->set('status', array('未取得', '取得中', '取得済み', '取得失敗'));

		$sellerNames = array();
		foreach ($sellers['sellers'] as $seller) {
			$sellerNames[] = $seller['name'];
		}

		if ($this->request->is('post')) {
			$this->layout = FALSE;
			$this->autoRender = FALSE;
			if (isset($this->request->data['Product'])) {
				$productIds = array_keys($this->request->data['Product']);
			} else {
				$productIds = array();
			}

			$products = $this->Product->find('all', array(
			'conditions'=> array(
				'Product.id'=> $productIds,
				'price <='=> $sellers['price']['max'],
				'price >='=> $sellers['price']['min'],
				'Seller.name'=> $sellerNames
				),
			'fields'=> array('Product.id', 'Product.name', 'Seller.name'),
			'order'=> array('Seller.name', 'Product.name')
			));

			return $this->writeProductCsv($products);
		}

		$products = $this->Product->find('all', array(
			'conditions'=> array(
				'price <='=> $sellers['price']['max'],
				'price >='=> $sellers['price']['min'],
				'Seller.name'=> $sellerNames
				),
			'order'=> array('Seller.name', 'Product.name')
			));
		$this->set('products', $products);
	}

	private function readSellerCsv($file) {
		if (($fp = fopen($file, 'r')) == FALSE) {
			return false;
		}
		$sellers = array();
		$header = true;
		while (($row = fgetcsv ($fp, 2)) !== FALSE) {
			if ($header) { //　ヘッダーを読まない。
				$header = false;
				continue;
			}
			//　セラーIDを取得。
			$me = $row[1];
			if (($mePos = strpos($me, 'seller=')) == FALSE) {
				if (($mePos = strpos($me, 'merchant=')) != FALSE) {
					$mePos += 9;
				}
			} else {
				$mePos += 7;
			}
			if ($mePos !== FALSE) {
				if (($mePosEnd = strpos($me, '&', $mePos)) == FALSE) {
					$me = substr($me, $mePos);
				} else {
					$me = substr($me, $mePos, $mePosEnd - $mePos);
				}
				// 出品者データを作成。
				$sellers[$me] = array('name'=> $row[0], 'status'=> 0);
			}
		}

		return $sellers;
	}

	private function writeProductCsv($products) {
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=商品一覧_'. date('YmdHis') .'.csv');

		$stream = fopen('php://output', 'w');
		fputcsv($stream, array(
			"出品者名",
			"タイトル",
			"ASINコード",
			"商品URL",
			"プライスチェックURL"
			)
		);
		foreach ($products as $product) {
			fputcsv($stream, array(
				$product['Seller']['name'],
				$product['Product']['name'],
				$product['Product']['id'],
				"http://www.amazon.co.jp/dp/" . $product['Product']['id'],
				"http://so-bank.jp/detail/?code=" . $product['Product']['id']
				)
			);
		}

		fclose($stream);
	}

}