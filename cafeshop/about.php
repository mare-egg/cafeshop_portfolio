<?php 
/**
 *ファイル名:about.php
 *サイト運営店舗の説明（テスト）
 * アクセスURL：http://localhost/SC/cafeshop/about.php
 */

namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;


$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

//シェアしたいURLまたはサイトの絶対パスを下記に設定
//例で文字「URL」を指定している
$share_url = 'URL';

//シェアしたいタイトル、店名など下記に記述
$share_title = '';

//twitterのアカウントを下記に指定
$twitter = '';


$context = [];
$context['share_url'] = $share_url;
$context['share_title'] = $share_title;
$context['twitter'] = $twitter;
$template = $twig->loadTemplate('about.html.twig');
$template->display($context);