$(function(){
  var entry_url = $("#entry_url").val();
  
  $("#update").click(function(){
    var up_item_id = $("#item_id").val();
    var up_num_id = $("#num_id").val();

    //location.href 現在ページのURLを参照する
    location.href = entry_url + "cart.php?up_item_id=" + up_item_id + "&up_num_id=" + up_num_id;
  });
});