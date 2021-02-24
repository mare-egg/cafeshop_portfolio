$(function() {
  var entry_url = $("#entry_url").val();
  
  $("#withdrawal").click(function(){
    //location.href 現在ページのURLを参照する
    if(window.confirm('本当に退会しますか？')){
      location.href = entry_url + "deleteflg.php";
    }
    else{
      window.alert('キャンセルされました');
    }
  });
});