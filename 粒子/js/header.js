window.onload=function(){
    var oA=document.querySelectorAll('.topMin .search li a');
    console.log(oA);
    for(var i=0;i<oA.length;i++){
        if(i%2==0){
            oA[i].style.color="#ff666b";
        }
    }
}