<?php
/*
 * 管理者画面
 * 会員登録時の内容
 * アクセスURL:http://localhost/SC/cafeshop/admin/detail2.php
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\master\initMaster;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Common;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, array(
  'cache' => Bootstrap::CACHE_DIR
));

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$initMaster = new initMaster();
 
if(isset($_GET['mem_id']) === true && $_GET['mem_id'] !== '') {
  $mem_id = $_GET['mem_id'];

  $table = 'member';
  $column = 'mem_id, family_name, first_name, family_name_kana, first_name_kana, sex, year, month, day, zip1, zip2, address, 
  email, tel1, tel2, tel3, cue, contents, regist_date';
  $where = ' mem_id = ' . $mem_id;
  $data = $db->select($table,$column,$where);

  $dataArr = ($data !== "" && $data !== []) ? $data[0] : '';
  $dataArr['cue'] = explode('_', $dataArr['cue']);
  $context = [];
  $context['cueArr'] = $initMaster->getCueArr();
  $context['dataArr'] = $dataArr;
  $template = $twig->loadTemplate('detail2.html.twig');
  $template->display($context);
} else {
  header('Location:' . Bootstrap::ENTRY_URL . 'admin/list2.php');
  exit();
}