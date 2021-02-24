<?php
/**
 * お問い合わせ画面
 * http://localhost/SC/cafeshop/contact/index.php
 */
include 'sendmail.php';
require_once dirname(__FILE__) . '../../Bootstrap.class.php';

use cafeshop\Bootstrap;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$context = [];
$context['alert'] = $alert;

$template = $twig->loadTemplate('contact.html.twig');
$template->display($context);