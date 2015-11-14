<?php

// pr ($var)

App::uses('AppController', 'Controller');
require_once(APP . 'Vendor' . DS . 'simple_html_dom.php');

class ProductsController extends AppController {

	public $uses = array('Seller', 'Product', 'Price');

	public function product() {
		$this->autoRender = FALSE;
		if (!$this->request->is('ajax')) {
			return $this->redirect(array('controller'=> 'sellers'));
		}
		// 最高価格を確認
		$price = $this->Session->read('sellers');
		if ($price == null) {
			return -1;
		} else {
			$price = $price['price'];
		}
		// リクエストデータを取得
		$seller_id = trim($this->request->data['id']);
		$seller_name = trim($this->request->data['name']);

		if ($seller_id == '' || $seller_name == '') {
			return -1;
		}

		// キャッシュ時間内、データを返す。
		$seller = $this->Seller->findByMe($seller_id);
		if ($seller && $this->isValid($seller['Seller']['updated_date'])) {
			$id = $seller['Seller']['id'];
		}
		// データがない場合、又はデータが古い場合、新データを取得する。
		else {
			$id = $this->crawlAmazon($seller_id, $seller_name, $seller ? $seller['Seller']['id'] : null);
		}

		if ($id == FALSE) {
			return -1;
		}
		$products = $this->Product->find('count', array('conditions'=> array('seller_id'=> $id, 'price <='=> $price)));

		return $products;
	}

	public function price() {
		$this->autoRender = FALSE;
		if (!$this->request->is('ajax')) {
			return $this->redirect(array('controller'=> 'sellers'));
		}
		$asin = 'B00GOM4NQA';

		$graph = $this->Price->findById($asin);

		// キャッシュ時間内、データを返す。
		if ($graph && $this->isValid($graph['Price']['updated_date'])) {
			$graph = $graph['Price']['graph'];
		}
		// データがない場合、又はデータが古い場合、新データを取得する。
		else {
			try {
				$graph = $this->crawlPrice($asin);
			} catch (Exception $ex) {
				$graph = null;
			}
		}

		return array('asin'=> $asin, 'graph'=> $graph);
	}

	private function isValid($date) {
		return ((time() - 86400) < strtotime($date));
	}

	private function crawlAmazon($seller_id, $seller_name, $id=null) {
		$updated_by = $this->Auth->User('id');
		$updated_date = date('Y-m-d H:i:s');

		//　出品者情報はDBに保存する。
		$seller = array(
				'me'=> $seller_id,
				'name'=> $seller_name,
				'updated_by'=> $updated_by,
				'updated_date'=> $updated_date
			);
		if ($id != null) $seller['id'] = $id;
		$this->Seller->save($seller);
		
		$maxPage = -1;
		$curPage = 1;
		while (true) {
			try {
				$max_page = $this->crawlAmazonProduct($seller_id, $curPage, $this->Seller->id, $updated_by, $updated_date);
				if ($curPage == 1) {
					$maxPage = $max_page;
				}
				if ($curPage >= $maxPage) break;
				$curPage ++;
			} catch (Exception $ex) {
				$maxPage = -1;
				$this->Seller->delete($this->Seller->id);
				return FALSE;
			}
		}

		return $this->Seller->id;
	}

	private function crawlAmazonProduct($seller_id, $page, $db_seller_id, $updated_by, $updated_date) {
		$max_page = 0;

		$url = substr(AMAZON, 0, 35) . $seller_id . substr(AMAZON, 35) . $page;
		// アマゾンサイトをクロールする。
		$html = file_get_html($url);
		// 最初にページ数を取得する。
		if ($page == 1) {
			$max_page = $html->find('div[id=pagn]', 0);
			$max_page = $max_page->last_child()->prev_sibling()->prev_sibling();

			$class = $max_page->class;
			if (strpos($class,'pagnLink') == false) {
				$max_page = trim($max_page->plaintext);
			} else {
				$max_page = $max_page->find('a', 0);
				$max_page = trim($max_page->plaintext);
			}

			$max_page = intval($max_page);
		}
		// 商品情報を取得する。
		$html = $html->find('div[id=resultsCol] li.s-result-item');
		$products = array();
		foreach ($html as $li) {
			if (!isset($li->attr['data-asin'])) continue;
			// ASINコード
			$asin = $li->attr['data-asin'];
			// 商品名
			$name = $li->find('div.s-item-container div.a-spacing-mini a.s-access-detail-page', 0);
			$name = trim($name->plaintext);
			// 価格
			$price = $li->find('div.s-item-container div.a-spacing-mini a.a-link-normal span.s-price', 0);
			if ($price) {
				$price = str_replace(array('￥ ', ','), '', trim($price->plaintext));
				$price = intval($price);
			} else {
				$price = 0;
			}

			// TODO: 同じ商品だけど、他の店でもあるみたい。
			$products[] = array(
				'id'=> $asin,
				'name'=> $name,
				'price'=> $price,
				'seller_id'=> $db_seller_id,
				'updated_by'=> $updated_by,
				'updated_date'=> $updated_date
				);
		}
		// DBに商品を保存する。
		$this->Product->saveMany($products);

		return $max_page;
	}

	private function crawlPrice($asin) {
		// プライスチェックサイトをクロールする。
		$html = file_get_html(PRICE . $asin);
		// ランキング変動グラフの作成スクリプトとはフッダーにあるため、フッダーを探す。
		$html = $html->find('div[id=footer]', 0);
		// 最後のスクリプトはランキング変動のスクリプトである。
		$html = $html->find('script', '-1');
		$html = $html->innertext;

		//　クロールしたデータはDBに保存する。
		$data = array(
				'id'=> $asin,
				'graph'=> $html,
				'updated_by'=> $this->Auth->User('id'),
				'updated_date'=> date('Y-m-d H:i:s')
			);
		$this->Price->save($data);

		return $html;
	}

}