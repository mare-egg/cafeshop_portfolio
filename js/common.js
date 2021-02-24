$(function() {
  $('#address_search').click(function() {

    // .val() 要素のvalue値を取得します
    var zip1 = $('#zip1').val();
    // IDzip1の値をとり、その中のvalをvar zpi1に入れる
    var zip2 = $('#zip2').val();
    var entry_url = $('#entry_url').val(); 
    
    if (zip1.match(/[0-9]{3}/) === null || zip2.match(/[0-9]{4}/) === null) {
      alert('正確な郵便番号を入力してください。');
      return false; //ページ
    } else {
      //javascriptからphpを呼び出すことができるajax
      //連想配列  typeにget
      //          urlにentry_url + "/postcode_search.php?zip1=" + escape(zip1) + "&zip2=" + escape(zip2)
      $.ajax({
        type : "get", //GET通信
        //ajaxが成功したが、データがない場合
        //下記ソースを読み込まれる
        url : entry_url + "/postcode_search.php?zip1=" + escape(zip1) + "&zip2=" + escape(zip2),
      }).then(
      //成功したら下記
        function(data){
          //空のまま押されたもしくはdataがない場合
          if(data == 'no' || data == '' ) {
            alert('該当する郵便番号がありません');
          } else {
            //valのなかのdataに書き込む
            $('#address').val(data);
          }
        },
      );
    }
  });
});