$(function(){

  $("#sendButton").click(function(){

    //値の初期化
    init();

    //textのエラーチェック
    error_check = true;
    var name = $('#name_id').val();
    if(name === ''){
      error_check = false;
      $('#name_id').css("background-color","#FFC0CB");
    }

    var age= $('#age_id').val();
    if(age === ''){
      error_check = false;
      $('#age_id').css("background-color","#FFC0CB");
    }

    //radioのエラーチェック
    var sex_ele = $('input[name = "sex"]');
    if(sex_ele[0]['checked'] === false && sex_ele[1]['checked'] === false) {
      $("#sex_area").css("background-color","#FFC0CB");
    }

    //selectのエラーチェック
    var language = $("#language_id").val();
    if(language === '') {
      error_check = false;
      $('#language_id').css("background-color","#FFC0CB");
    }

    if(error_check === true) {
      document.forms[0].submit();
    } else {
      return false;
    }
  });

  //色の初期化
  function init()
  {
    $('input[type=text],input[type=file]').css("background-color","");
    $('#sex_area').css('background-color','');
    $("select").css('background-color','');
  }
});