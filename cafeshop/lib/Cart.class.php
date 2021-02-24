<?php
/**
 * ファイル名：Cart.class.php
 * カートに関するプログラムのクラスファイル
 * ・カート/在庫/注文/金額計算
 */
namespace cafeshop\lib;

class Cart
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  //カートに商品を登録する
  public function insCartData($customer_no, $item_id, $num_id)
  {
    $table = 'cart';
    $insData = [
      'customer_no' => $customer_no,
      'item_id' => $item_id,
      'num' => $num_id
    ];
    return $this->db->insert($table, $insData);
  }

  //注文確定時に会員ID、日時、商品ID、数量、合計金額を登録する
  public function insOrderConfirmed($mem_id,$date,$item_id,$num,$singlesumprice)
  {
    $table = 'orderconfirmed';
    $insData = [
      'mem_id' => $mem_id,
      'date' => $date,
      'item_id' => $item_id,
      'sumnum' => $num,
      'sumprice' => $singlesumprice
    ];
    return $this->db->insert($table, $insData);
  }

  //カートの情報を取得する
  public function getCartData($customer_no)
  {
    $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
    $column = ' c.crt_id, i.item_id, i.item_name, i.price, i.image, c.num, i.stock, c.num * i.price AS singlePrice';
    $where = ' c.customer_no = ? AND c.delete_flg = ? AND c.item_id ';
    $arrVal = [$customer_no,0];

    return $this->db->select($table,$column,$where,$arrVal);
  }

  //カートに入れる際に同じ商品の場合、数量のみカウントする処理で使用
  //カート内から特定のデータを取得
  public function startgetCartData($customer_no,$item_id)
  {

    $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id ';
    $column = ' c.crt_id, i.item_id, i.item_name, i.price, SUM(c.num) AS num, i.stock';
    $where = ' c.customer_no = ? AND c.delete_flg = ? AND i.item_id = ?';
    $arrVal = [$customer_no,0,$item_id];

    return $this->db->select($table,$column,$where,$arrVal);
  }

  //カート情報を削除する（フラグを立てる）
  public function delCartData($crt_id)
  {
    $table = ' cart ';
    $insData = ['delete_flg' => 1];
    $where = ' crt_id = ?';
    $arrWhereVal = [$crt_id];

    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }

  //購入された商品の在庫数をUPDATEする
  public function updateStock($stock,$item_id)
  {
    $table = ' item ';
    $insData = ['stock' => $stock];
    $where = ' item_id = ?';
    $arrWhereVal = [$item_id];

    return $this->db->update($table,$insData,$where,$arrWhereVal);
  }

  //商品数と合計金額を取得する
  public function getItemAndSumPrice($customer_no)
  {
    $table = " cart c LEFT JOIN item i ON c.item_id = i.item_id ";
    $column = " SUM(c.num * i.price) AS totalPrice ";
    $where = ' c.customer_no = ? AND c.delete_flg = ?';

    $arrWhereVal = [$customer_no, 0];
 
    $res = $this->db->select($table,$column,$where,$arrWhereVal);
    $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;

    $table = ' cart c ';
    $column = ' SUM( num ) AS num ';
    $where = ' c.customer_no = ? AND c.delete_flg = ?';

    $res = $this->db->select($table,$column,$where,$arrWhereVal);

    $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
    return [$num, $price];
  }

  //注文履歴を最新データ15件分取得する
  public function selectpurchase($mem_id) 
  {
    $table = ' orderconfirmed ';
    $col = ' item.item_id, date, item.item_name, sumnum, sumprice ';
    $where = ($mem_id !== '') ? ' mem_id = ? ' : '';
    $arrVal = ($mem_id !== '') ? [$mem_id] : [];
    $leftjoin = ' item ';
    $this->db->setLeftJoin($leftjoin);
    $on = ' orderconfirmed.item_id = item.item_id ';
    $this->db->setOn($on);
    $order = ' date DESC limit 15';
    $this->db->setOrder($order);

    
    $res = $this->db->selectReview($table,$col,$where,$arrVal);
    
    return $res;
  }
}