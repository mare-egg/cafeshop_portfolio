<?php
/**
 * //http://localhost/SC/cafeshop/resetpassconfirm.php
 * メール送信後のパスワード再設定画面
 */
namespace cafeshop;

require_once dirname (__FILE__) . '/Bootstrap.class.php';

use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Reset;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$reset = new Reset($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);
$context = [];

$message = '';
$error = '';

//URLをクリック時
if(isset($_GET['passReset'])){
  //GETのトークンを取得
  $passResetToken = $_GET['passReset'];

  //取得したトークンと同じデータを取得
  $data = $reset->selectreset($passResetToken);

  if(!empty($data)){
    //正しく取得できた時
    $reset_id = $data[0]["reset_id"];//登録ID
    $insdate = $data[0]["date"];//有効日時のリミット(登録日時の+30分)
    $mem_id = $data[0]["mem_id"];//会員ID
    $limitTime = date("Y-m-d H:i:s", strtotime("-1 minute"));

    if(strtotime($insdate) >= strtotime($limitTime)){
      //30分以内のため有効
      //ここで初めてパスワードをPOSTで受け取り、エラーないかチェック
      //okの場合のみUPDATE実行

      //ボタン押された時
      if (isset($_POST["reset"])) {
        //パスワード入力チェック
        if (empty($_POST["password"])) {
          $error = 'パスワードが未入力です。';
        } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $_POST["password"])){
          $error = '半角英数字のみ使用してください';
        } elseif (mb_strlen($_POST["password"]) < 6) {
          $error = 'パスワードは6文字以上で設定してください';
        } elseif (mb_strlen($_POST["password"]) > 32) {
          $error = 'パスワードが長すぎます。32文字以下で設定してください';
        }

        //パスワード確認用
        if (empty($_POST["password2"])) {
          $error = 'パスワードが未入力です。';
        } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $_POST["password2"])){
          $error = '半角英数字のみ使用してください';
        } elseif (mb_strlen($_POST["password2"]) < 6) {
          $error = 'パスワードは6文字以上で設定してください';
        } elseif (mb_strlen($_POST["password2"]) > 32) {
          $error = 'パスワードが長すぎます。32文字以下で設定してください';
        }
          
        if (!empty($_POST["password"]) && !empty($_POST["password2"]) && $error === '') {
          // 入力したユーザIDを格納
            $password = $_POST["password"];
            $password2 = $_POST["password2"];
            
            if($password === $password2) {
              //パスワードが確認用と一致しているため
              //パスワードをハッシュしてmemberのパスワードをUPDATE
              //$mem_idのあるパスワードをUPDATEする
              $date = date("Y-m-d H:i:s");
              $res = $reset->updatepassword($password,$mem_id,$date);
              if($res === true) {
                $message = 'パスワードが更新されました';
              } else {
                $error = 'パスワード更新に失敗しました';
              }
            } else {
              $error = 'パスワードと確認用パスワードが一致しておりません。';
            }
        }
      }
    } else {
      //登録日時から30分経過しているため無効
      //DBを削除SQL実行
      $del = $reset->deleterestpass($reset_id);

      if($del === true) {
        
        $error = 'お客様のURLは30分経過しているため無効となっております。恐れ入りますが、初めからやり直して下さい。';
      } else {
        //削除失敗
      }
    }
  } else {
    //データがSELECTできなかった場合($tokenがDBに登録されていない場合)
    $error = 'お客様のURLは30分経過しているため無効となっております。恐れ入りますが、初めからやり直して下さい。';
  }
} else {
  $error = '通信によるエラーが発生しました。恐れ入りますが、やり直してください。';
}

$ses->checkSession();
$context['message'] = $message;
$context['error'] = $error;
$template = $twig->loadTemplate('resetpassconfirm.html.twig');
$template->display($context);