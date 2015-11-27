-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: 2015 年 11 月 16 日 12:47
-- サーバのバージョン： 5.5.38
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `researchamc_amacheck`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `prices`
--

CREATE TABLE `prices` (
  `id` char(10) NOT NULL COMMENT 'ASINコード',
  `graph` text NOT NULL COMMENT 'プライスチェック',
  `updated_by` bigint(20) NOT NULL COMMENT '更新者',
  `updated_date` datetime NOT NULL COMMENT '更新日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `products`
--

CREATE TABLE `products` (
  `id` char(10) NOT NULL COMMENT 'ASINコード',
  `name` varchar(200) NOT NULL COMMENT '商品名',
  `price` int(11) NOT NULL COMMENT '価格',
  `seller_id` bigint(20) NOT NULL COMMENT 'セラーID',
  `updated_by` bigint(20) NOT NULL COMMENT '更新者',
  `updated_date` datetime NOT NULL COMMENT '更新日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `productinfos`
--

CREATE TABLE `productinfos` (
  `id` char(10) NOT NULL DEFAULT '' COMMENT 'ASINコード',
  `min_price` varchar(20) NOT NULL COMMENT '最安',
  `num_sellers` varchar(10) NOT NULL COMMENT '新品の出品者数',
  `updated_by` bigint(20) NOT NULL COMMENT '更新者',
  `updated_date` datetime NOT NULL COMMENT '更新日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `sellers`
--

CREATE TABLE `sellers` (
`id` bigint(20) unsigned NOT NULL,
  `me` varchar(50) NOT NULL COMMENT 'セラーID',
  `name` varchar(200) NOT NULL COMMENT '出品者名',
  `updated_by` bigint(20) NOT NULL COMMENT '更新者',
  `updated_date` datetime NOT NULL COMMENT '更新日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
`id` bigint(20) unsigned NOT NULL,
  `username` varchar(50) NOT NULL COMMENT 'ログインID',
  `password` varchar(500) NOT NULL COMMENT 'パスワード',
  `name` varchar(50) NOT NULL COMMENT '氏名',
  `role` int(11) NOT NULL DEFAULT '0' COMMENT '権限：０（一般者）、９９９（管理者）',
  `created_by` bigint(20) NOT NULL COMMENT '作成者',
  `updated_by` bigint(20) NOT NULL COMMENT '更新者',
  `created_date` datetime NOT NULL COMMENT '作成日時',
  `updated_date` datetime NOT NULL COMMENT '更新日時'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `created_by`, `updated_by`, `created_date`, `updated_date`) VALUES
(1, 'hai', '$2a$10$nV9W5w4gKjUiAh377XtNfuQL/uN..by/M8BC1gVkHCxNW.DKpou8.', '管理者　ドーミンハイ', 999, 1, 1, '2015-11-16 20:27:57', '2015-11-16 20:27:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productinfos`
--
ALTER TABLE `productinfos`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;