
charset="UTF-8";

//查找元素
var username = document.getElementById("name");
var oBtn = document.getElementById("btn");
// username.value


//绑定事件
oBtn.onclick = function () {
    console.log(Number(username.value)+7);
};


var num = 3.1415926;
var str = "你好，北京";
var flag = true;
//声明对象
var abj = {
    name: "hsngbo",
    age: 45,
    sex: 1,
    work: ["actor", "singer", "director"],
    isMerrt: true
};
console.log(num);
console.log(str);
console.log(flag);


var arry = [];
var arry1 = [3, 4, 5, 3, 6, 7, 8, 54, 4, 345, 24, 643, 14];


//数据类型嵌套
var person = {
    name: "fanbingbing",
    age: 36,
    work: ["singer", "cator"],
    boyfriend: {
        name: "lichen",
        age: 40,
        work: "actor"
    }
}
var page= ((person.age+person.boyfriend.age)/2);
console.log(page);
console.log(person);
var movie = [{
    name: "zhouxingchi",
    age: "60"
}, {
    name: "zhuyin",
    age: "50"
}];


var school=[[30,25],[36,45],[24,52]];
console.log(school);


