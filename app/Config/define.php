<?php
$config=array();

// 権限
define('USER_ADMIN', 999);
define('USER_NORMAL', 0);

// クロールしたデータを無効にするタイム（単位：秒）
// 一日　（２４時間〜６０分〜６０秒）
define('CRAWL_TIMEOUT', 86400);
define('AMAZON', "http://www.amazon.co.jp/s?merchant=&page=");
define('PRICE', "http://so-bank.jp/detail?code=");
define('PRICE_RANKING_GRAPH', "graph3");

// セッション
define('SESSION_SELLER', 'sellers');
define('SESSION_CSV', 'checkedProds');
// ページ毎件数
define('PAGE_MAX', 30);