$(function(){
  var entry_url = $("#entry_url").val();
  
  $("#cart_in").click(function(){
    var item_id = $("#item_id").val();
    var num_id = $("#num_id").val();

    //location.href 現在ページのURLを参照する
    location.href = entry_url + "cart.php?item_id=" + item_id + "&num_id=" + num_id;
  });
});