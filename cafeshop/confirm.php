<?php
/*
 * ファイル名:confirm.php
 * アクセスURL:http://localhost/SC/cafeshop/confirm.php
 * 会員登録時のエラーがあれば登録画面へ戻す
 * エラーなく正しければ確認画面へ
 * 確認画面からユーザーが確定後、完了画面へ
 */
namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use cafeshop\master\initMaster;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Common;

// テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common();

// モード判定(どの画面から来たかの判断)
// 登録画面から来た場合
if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}

// 戻る場合
if (isset($_POST['back']) === true) {
  $mode = 'back';
}

// 登録完了
if (isset($_POST['complete']) === true) {
  $mode = 'complete';
}

// ボタンのモードによって処理をかえる
switch ($mode) {
  case 'confirm' :  // 新規登録
                    // データを受け継ぐ
                    // ↓この情報は入力には必要ない
      unset($_POST['confirm']);
      //confirmのキーを丸々消す
      
      $dataArr = $_POST;

      // この値を入れないでPOSTするとUndefinedとなるので未定義の場合は空白状態としてセットしておく
      if (isset($_POST['sex']) === false) {
        $dataArr['sex'] = "";
      }
      if (isset($_POST['cue']) === false) {
        $dataArr['cue'] = [];
      }

      // エラーメッセージの配列作成
      $errArr = $common->errorCheck($dataArr);
      $err_check = $common->getErrorFlg();

      $template = ($err_check === true) ? 'confirm.html.twig' : 'regist.html.twig';

      break;
  case 'back': // 戻ってきた時
              // ポストされたデータを元に戻すので、$dataArrに入れる
      $dataArr = $_POST;

      unset($dataArr['back']);

      // エラーも定義しておかないと、Undefinedエラー
      foreach ($dataArr as $key => $value) {
        $errArr[$key] = '';
      }

      $template = 'regist.html.twig';
      break;

  case 'complete': //登録完了
      $dataArr = $_POST;
      //↓この情報は要らないので外しておく
      unset($dataArr['complete']);

      $table = 'member';
      // $password = $_POST['password'];
      $res = $db->insertcomplete($table, $dataArr);

        if ($res === true) {
            // 登録成功時は完成ページへ遷移
            header('Location: ' . Bootstrap::ENTRY_URL . 'complete.php');
            exit();
        } else {
            // 登録失敗時は登録画面に戻る
            $template = 'regist.html.twig';

            foreach ($dataArr as $key => $value) {
                $errArr[$key] = '';
            }
        }
        break;
} 
$sexArr = initMaster::getSex();
$cueArr = initMaster::getCueArr();

$context['sexArr'] =  $sexArr;
$context['cueArr'] =  $cueArr;
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] =  $dataArr;
$context['errArr'] =  $errArr;
$template = $twig->loadTemplate($template);
$template->display($context);