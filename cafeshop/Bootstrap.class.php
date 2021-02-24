<?php
/* 
 * ファイル名:Bootstrap.class.php
 * 設定に関するプログラム
 */
namespace cafeshop;

date_default_timezone_set('Asia/Tokyo');

require_once '/Applications/XAMPP/xamppfiles/htdocs/SC/vendor/autoload.php';

class Bootstrap
{
  const DB_HOST = 'localhost';
  const DB_NAME = 'cafe_db';
  const DB_USER = 'cafe_user';
  const DB_PASS = 'cafe_pass';
  const DB_TYPE = 'mysql';

  const APP_DIR = '/Applications/XAMPP/xamppfiles/htdocs/SC/';

  const TEMPLATE_DIR = self::APP_DIR . 'templates/cafeshop/';

  const CACHE_DIR = self::APP_DIR . 'templates_c/cafeshop/';

  const APP_URL = 'http://localhost/SC/';

  const ENTRY_URL = self::APP_URL . 'cafeshop/';

  public static function loadClass($class)
  {
    $path = str_replace('\\','/',self::APP_DIR . $class . '.class.php');
    require_once $path;
  }
}

//オートローダー
spl_autoload_register([
  'cafeshop\Bootstrap',
  'loadClass'
]);
