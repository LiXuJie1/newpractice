 var whatshall;
 var c;
 
    

 
//  function whatshall( temp){
//      while(temp<100){
//         c=prompt("enter a number(please 40<enter<100)");
//      }
//      if(temp<60){
//          console.log("wear a jacket");
//      }
//      else if( temp<70){
//          console.log("wear a sweater");
//      }
//      else{
//          console.log("wear a T-shirt");
//      }
//  }
//  whatshall(50);
//  whatshall(80);
//  whatshall(60);
// whatshall(c);

// var a=1;
// var b=2;
// var sum=0;
// function doit(a,b){
//     sum=a+b;
// }
// doit(a,b);
// console.log(sum);



// 数组排数
// var a=[5,8,6,4,2,8,9,7,4,];





// 用户传入数字，先排成数组，再用冒泡排序排列大小
// 传入一次循环  用于用户输入可控制输入数字数量

// 冒泡排序两次循环
// 1：循环所有数字对其他数字进行一次进行一次比较
// 2：排除循环好的数字再从头进行一次排序

// var a=[i];
var a=new Array(i);
var y;
var i=0; 
var emp;


// 输出用户输入数字
function sortarry(arry){
    for(j=1;j<5;j++){
       a[i]= prompt("please enter four number which smaller than 5");
       i++;
}
console.log(a);
    for(y=0;y<a.length-1;y++ ){
        for(i =0;i<a.length-1-y;i++){
               if(a[i]>a[i+1]){
                   emp=a[i];
                   a[i]=a[i+1];
                   a[i+1]=emp;
               }
        }
    }
    return arry; 
}
//   if(a){
//       alert("牛逼");
//   }
 sortarry(a);
 console.log(a);






// var a=[i];
// var i=0;
// var emp;
// function sortarry(arry){
//       for(j=0;j<4;j++){
//             a[i]=prompt("please enter number which smaller than 10");
//             i++;  
//       }
//       console.log(a);
//         for(y=0;y<a.length-1;y++){
//             for(i=0;i<a.length-1-y;i++){
//                 if(a[i]>a[i+1]){
//                    emp=a[i];
//                    a[i]=a[i+1];
//                    a[i+1]=emp;  
//              }            
//             } 
//       }
// }
//   sortarry(a);
//   alert(a);

 
// sort自动排数方法   

// a=[5,1,8,7,3,6,2,4,]
// console.log(a.sort());




// 十以内奇数运算
// 用户输入形式是以字符串形式出入的，所以在要计算的前面加上parsenInt（）


// var a;
// var i=0;
// var sum=0;
       
// function run(){
//     for(i=0;i<10;i++){
//         a=prompt("please eter number which smaller htan 10");
//       console.log(a);
//           if(a%2!=0){
//             sum=parseInt(sum)+parseInt(a);
//             console.log(sum);
//           }
//       }
//      }
    
    
//     run();







    // console.log(sum);
    // for(j=0;j<a.length;j++){
    //     for(i=0;i<a.length)
    // }






