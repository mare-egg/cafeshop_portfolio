<?php
/*
 * ファイル名:initMaster.class.php
 * 会員登録時に必要な設定ファイル
 * 生年月日/性別/きっかけの選択肢
 */
namespace cafeshop\master;

class initMaster
{
  public static function getDate()
  {
    $yearArr = [];
    $monthArr = [];
    $dayArr = [];

    //'Y'現在の年が返ってくる
    //dateは現在の日付もしくは別の日付を指定して取得できる
    $next_year = date('Y') - 80;

    //年を作成
    for ($i =2022; $i > $next_year; $i--) {
      //第二引数の文字列を絶対4桁で表示sprintf 
      $year = sprintf("%04d",$i);
      $yearArr[$year] = $year . '年';
      
      /**
       * $yearArr = [
       *    '1900' => '1900',
       *    '1901' => '1901',
       *    ....
       *    '2020' => '2020',
       * ],
       */
    }

    //月を作成  1〜12
    for($i = 1; $i < 13; $i++) {
      $month = sprintf("%02d", $i);
      $monthArr[$month] = $month . '月';
    }
    //日を作成  1〜31
    for($i = 1; $i < 32; $i++) {
      $day = sprintf("%02d", $i);
      $dayArr[$day] = $day . '日';
    }

    return [$yearArr,$monthArr,$dayArr];
  }

  public static function getSex()
  {
    $sexArr = ['1' => '男性' , '2' => '女性'];
    return $sexArr;
  }

  public static function getCueArr()
  {
    $cueArr = ['ネット記事','Web検索','SNS','紹介','雑誌・新聞','その他'];
    return $cueArr;
  }
}