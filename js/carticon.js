function ShowCart(){
  var MyCart = document.getElementById("carticon");
  if(MyCart.innerHTML == "0"){
    MyCart.style.display = "none";
  }else{
    MyCart.style.display = "block";
  }
}
function SetCart(num){
  var MyCart = document.getElementById("carticon");
  MyCart.innerHTML = num;
  ShowCart();
}