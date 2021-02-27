<?php
/**
 * カート内からの注文確認画面
 * http://localhost/SC/cafeshop/orderconfirm.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Cart;
use cafeshop\lib\Mypage;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);
$mypage = new Mypage($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

$ses->checkSession();
$customer_no = $_SESSION['customer_no'];

$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/',$_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';
if($crt_id !== '') {
  $res = $cart->delCartData($crt_id);
}

//会員情報を取得し、確認させる情報を取得する
if(isset($_SESSION['mem_id'])) {
  $mem_id = $_SESSION['mem_id'];
  $memberdata = $mypage->selectConfirmCart($mem_id);
  $context['dataMember'] = $memberdata;
}

$dataArr = $cart->getCartData($customer_no);

//商品の合計金額・数を取得する
list($sumNum,$sumPrice) = $cart->getItemAndSumPrice($customer_no);

$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$template = $twig->loadTemplate('orderconfirm.html.twig');
$template->display($context);