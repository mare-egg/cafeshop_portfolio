<?php
/*
 * ファイル名:regist.php
 * アクセスURL:http://localhost/SC/cafeshop/regist.php
 * ユーザー新規登録による画面表示
 */
namespace cafeshop;

require_once dirname (__FILE__) . '/Bootstrap.class.php';

use cafeshop\master\initMaster;
use cafeshop\Bootstrap;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

//初期データを設定
$dataArr = [
  'family_name' => '',
  'first_name' => '',
  'family_name_kana' => '',
  'first_name_kana' => '',
  'user_name' => '',
  'password' => '',
  'sex' => '',
  'year' => '',
  'month' => '',
  'day' => '',
  'zip1' => '',
  'zip2' => '',
  'address' => '',
  'email' => '',
  'tel1' => '',
  'tel2' => '',
  'tel3' => '',
  'cue' => '',
  'contents' => ''
];

$errArr = [];
foreach($dataArr as $key => $value) {
  $errArr[$key] = '';
}

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$sexArr = initMaster::getSex();
$cueArr = initMaster::getCueArr();

$context = [];

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['cueArr'] = $cueArr;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate('regist.html.twig');
$template->display($context);