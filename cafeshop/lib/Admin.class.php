<?php
/**
 * ファイル名：Admin.class.php
 * 管理者から操作する際のクラスファイル
 * 
 */
namespace cafeshop\lib;

class Admin
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  public function delMemberData($mem_id)
  {
    $table = ' member ';
    $insData = ['delete_flg' => 1];
    $where = ' mem_id = ? ';
    $arrWhereVal = [$mem_id];
    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }

  public function enableMemberData($mem_id)
  {
    $table = ' member ';
    $insData = ['delete_flg' => 0];
    $where = ' mem_id = ? ';
    $arrWhereVal = [$mem_id];
    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }
}