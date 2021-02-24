<?php
/**
 * ファイル名：Login.class.php
 * ログインに関するプログラムのクラスファイル
 */
namespace cafeshop\lib;

class Login
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  public function loginSelect($user_name)
  {
    $table = ' member ';
    $col = '';
    $where = ($user_name !== '') ? ' user_name = ? AND delete_flg = 0 ' : '';
    $arrVal = ($user_name !== '') ? [$user_name] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function logintrueSelect($mem_id)
  {
    $table = ' member ';
    $col = '';
    $where = ($mem_id !== '') ? ' mem_id = ? AND delete_flg = 0 ' : '';
    $arrVal = ($mem_id !== '') ? [$mem_id] : [];

    $res = $this->db->select($table,$col,$where,$arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }
}