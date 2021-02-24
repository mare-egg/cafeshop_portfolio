<?php
//http://localhost/SC/cafeshop/login.php

namespace cafeshop;

require_once dirname(__FILE__) . '/Bootstrap.class.php';
use cafeshop\Bootstrap;
use cafeshop\lib\PDODatabase;
use cafeshop\lib\Session;
use cafeshop\lib\Login;

$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$ses = new Session($db);
$login = new Login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["user_name"])) {
        $errorMessage = 'ユーザー名が未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["user_name"]) && !empty($_POST["password"])) {
        // 入力したユーザIDを格納
        $user_name = $_POST["user_name"];
        $context['username'] = $user_name;
        $context['htmlspecialchars2'] = htmlspecialchars ($user_name, ENT_QUOTES);

        $res = $login->loginSelect($user_name);

        if(!empty($res) === true) {
            $password = $_POST["password"];
            $hash = $res[0]['password'];

            if (password_verify($password, $hash)) {
                session_regenerate_id(true);

                $mem_id = $res[0]['mem_id'];
                $row = $login->logintrueSelect($mem_id);
                $_SESSION['mem_id'] = $row[0]['mem_id'];
                $_SESSION['user_name'] = $row[0]['user_name'];
                header("Location: http://localhost/SC/cafeshop/list.php");
                exit();
            } else {
                $errorMessage = 'パスワードに誤りがあります。';
            }
        } else { 
            // 認証失敗
            $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
        }
    }
} 


$context = [];
$context['htmlspecialchars'] = htmlspecialchars($errorMessage, ENT_QUOTES);
$template = $twig->loadTemplate('login.html.twig');
$template->display($context);