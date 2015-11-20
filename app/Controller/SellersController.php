<?php

App::uses('AppController', 'Controller');

class SellersController extends AppController {

	public $uses = array('Seller', 'Product', 'Price');

	public $components = array('Paginator');
	public $paginate = array(
			'fields'=> array(
				'Product.id',
				'Product.name',
				'Product.seller_id',
				'Seller.name'
			),
			'order'=> array(
				'Product.seller_id'=> 'asc',
				'Product.id'=> 'asc'
			)
		);

	public function index() {
		$this->set('title_for_layout', '商品一覧');
		if ($this->request->is('post') || $this->request->is('put')) {
			// バリデーションを行う。
			$this->Seller->set($this->request->data);
			if ($this->Seller->validates()) {
				$data = $this->request->data['Seller'];
				if (isset($data['file']['error']) && $data['file']['error'] == 0) {
					$this->Session->delete(SESSION_SELLER);
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
							$this->Session->write(SESSION_SELLER,
								array('price'=> array('min'=> $data['price_min'], 'max'=> $data['price_max']),
										'sellers'=> $sellers));
						}
						unlink($data['file']['tmp_name']);
					}
				} else if ($this->Session->read(SESSION_SELLER) == null) {
					$this->Session->setFlash(__('出品者ファイルをアップロードしてください。'), 'default', array(), 'upload');
				} else {
					$sellers = $this->Session->read(SESSION_SELLER);
					$sellers['price'] = array('min'=> $data['price_min'], 'max'=> $data['price_max']);
					$this->Session->write(SESSION_SELLER, $sellers);
				}
			}
		}
		// 出品者一覧を読み込み
		if (($sellers = $this->Session->read(SESSION_SELLER)) != null) {
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
		if (($sellers = $this->Session->read(SESSION_SELLER)) == null) {
			return $this->redirect(array('action'=> 'index'));
		}

		$this->set('title_for_layout', 'ランキング変動');
		$this->set('load_graph', true);
		$this->set('status', array('未取得', '取得中', '取得済み', '取得失敗'));

		$sellerIds = array_keys($sellers['sellers']);

		if ($this->request->is('post') || $this->request->is('put')) {
			// 選択された商品をセッションに保存
			$productIds = $this->Session->read(SESSION_CSV);
			if ($productIds == null) {
				$productIds = array();
			}
			if (isset($this->request->data['Product'])) {
				$productIds = $this->request->data['Product'] + $productIds;
				// 選択されない商品を保存しない
				$removeKeys = array_keys($productIds, 0);
				foreach ($removeKeys as $key) {
					unset($productIds[$key]);
				}
				$this->Session->write(SESSION_CSV, $productIds);
			}
			
			// CSV出力
			if (isset($this->request->data['csv'])) {
				$this->layout = FALSE;
				$this->autoRender = FALSE;
				
				// 選択された商品のみ出力
				$productIds = array_keys($productIds);
				if (empty($productIds)) {
					$products = $productIds;
				} else {
					$products = $this->Product->find('all', array(
						'conditions'=> array(
							'Product.id'=> $productIds,
							'price <='=> $sellers['price']['max'],
							'price >='=> $sellers['price']['min'],
							'Seller.me'=> $sellerIds
							),
						'fields'=> array('Product.id', 'Product.name', 'Product.seller_id', 'Seller.name'),
						'order'=> array('Product.seller_id'=> 'asc', 'Product.id'=> 'asc')
					));
				}

				return $this->writeProductCsv($products);
			}
			// 現ページ
			if (isset($this->request->data['Control']['page'])) {
				$this->request->params['named']['page'] = $this->request->data['Control']['page'];
			}
		} else { // GET ページ初期化
			$this->Session->delete(SESSION_CSV);
		}

		$this->Paginator->settings = $this->paginate;
		// ページ毎件数
		if (empty($this->request->data['Control']['max'])) {
			$this->Paginator->settings['limit'] = PAGE_MAX;
			$this->request->data['Control']['max'] = PAGE_MAX;
		} else {
			$this->Paginator->settings['limit'] = $this->request->data['Control']['max'];
		}
		
		try {
			$this->Paginator->settings['conditions'] = array(
					'price <='=> $sellers['price']['max'],
					'price >='=> $sellers['price']['min'],
					'Seller.me'=> $sellerIds
				);
			$products = $this->Paginator->paginate('Product');
		} catch (NotFoundException $e) {
			$products = array();
		}

		$this->set('products', $products);

		// 現ページ
		if (empty($this->request->params['named']['page'])) {
			$this->request->data['Control']['page'] = 1;
		} else {
			$this->request->data['Control']['page'] = $this->request->params['named']['page'];
		}
	}

	private function readSellerCsv($file) {
		$buf = mb_convert_encoding(file_get_contents($file), 'UTF-8', 'SJIS-win');
		$lines = explode("\r\n", $buf);
		// 末尾の改行で分割された行を無視するためarray_pop()している
		array_pop($lines);

		$sellers = array();
		$header = true;
		foreach ($lines as $line) {
			$row = str_getcsv($line);
			if ($header) { //　ヘッダーを読まない。
				$header = false;
				continue;
			}
			//　セラーIDを取得。
			$me = $row[1];
			// 空白セル以降はデータなし
			if (empty($me)) break;

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
			mb_convert_encoding("出品者名", "SJIS-win", "UTF-8"),
			mb_convert_encoding("タイトル", "SJIS-win", "UTF-8"),
			mb_convert_encoding("ASINコード", "SJIS-win", "UTF-8"),
			mb_convert_encoding("商品URL", "SJIS-win", "UTF-8"),
			mb_convert_encoding("プライスチェックURL", "SJIS-win", "UTF-8"),
			)
		);
		foreach ($products as $product) {
			fputcsv($stream, array(
				mb_convert_encoding($product['Seller']['name'], "SJIS-win", "UTF-8"),
				mb_convert_encoding($product['Product']['name'], "SJIS-win", "UTF-8"),
				$product['Product']['id'],
				"http://www.amazon.co.jp/dp/" . $product['Product']['id'],
				"http://so-bank.jp/detail/?code=" . $product['Product']['id']
				)
			);
		}

		fclose($stream);
	}

}