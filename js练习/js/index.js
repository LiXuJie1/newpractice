
// 99瓶啤酒经典歌词在 console里查看
var word = "bottles";
var count = 99;
while (count > 0) {
    console.log(count + "" + word + " of beer on the wall");
    console.log(count + "" + word + " of beer,");
    console.log("Take one down,pass it around,");
    count = count - 1;
    if (count > 0) {
        console.log(count + "" + word + " of beer on the wall.");
    } else {
        console.log("No more" + word + " of beer on the wall.");
    }
}

//
//
//
//
// 设置战舰三个部位的坐标
var RandomLocation=Math.floor(Math.random()*5);
var location1 = RandomLocation;
var location2 = RandomLocation+1;
var location3 = RandomLocation+2;
var guess;
// 用户猜测变量
var hits = 0;
// 击中数量
var guesses = 0;
// 用户猜测数量，用来计算分数
var isSunk = false;
// 设置是否击沉为否

var repeat;
// var repeat2=0;
while (isSunk == false) {
    guess = prompt("Ready,aim ,fire!(enter a number 0-6:)");
    if (guess <= 0 || guess > 6) {
        alert("please enter a valid cell number!");
    } else {
        guesses = guesses + 1;
    }
    if (guess>0 && guess<7 &&(guess == location1 || guess == location2 || guess == location3)&& guess!=repeat &&guess!=repeat2) {
        hits = hits + 1;
        repeat=guess;
        // if(repeat2=0){
        //     repeat2=repeat;
        
            
        // }
        alert("beautiful,you hit it");
        if (hits == 3) {
            alert("You sank my battleship");
            break;  
        }
    } 
      if (guess>0 &&guess<7 && (guess!=location1 && guess!=location2 && guess!=location3)){
        alert("miss");
    }
  
}
var stats = "you take " + guesses + " guesses to sink the battleship," + "which means your shooting accuracy was " + (3 / guesses);
alert(stats);

var text=1;
function doit(emp){
    emp=2;
}
doit(text);
console.log(text);