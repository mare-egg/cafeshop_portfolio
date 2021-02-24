<?php
/*
 * アクセスURL:http://localhost/SC/cafeshop/admin/list2.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Admin;

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER, Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$admin = new Admin($db);

if(isset($_GET['mem_id']) && isset($_GET['delete_flg'])){
  $mem_id = $_GET['mem_id'];
  $delete_flg = $_GET['delete_flg'];
  //有効:0 無効:1 
  if($delete_flg === '0') {
    //有効化
    $res = $admin->enableMemberData($mem_id);
  } elseif ($delete_flg === '1') {
    //無効化
    $res = $admin->delMemberData($mem_id);
  }
}

$table = 'member';
$column = 'mem_id, family_name, first_name, family_name_kana, first_name_kana, sex, email, cue, regist_date, delete_flg';
$dataArr = $db->select($table,$column);

$context = [];
$context['dataArr'] = $dataArr;
$template = $twig->loadTemplate('list2.html.twig');
$template->display($context);