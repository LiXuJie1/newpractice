window.onload = function () {
    var oImg = document.querySelectorAll('.imgBox img');
    var oBor = document.querySelectorAll('.bor');
    new WOW().init();
    var mySwiper = new Swiper('.swiper-container', {
        direction: 'horizontal',
        loop: true,
        // 头部分页器
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: function (index, className) {
                return '<span class="re ' + className + '">' + ('0' + (index + 1)) + '<em class="line ab"></em></span>';
            },
        },

        // 前进后退按钮
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    })
    // 创新灯具分页器
    var mySwiper = new Swiper('.shopGo', {
        // slidesPerView: 1,
        // slidesPerGroup: 1,
        centeredSlides: true,
        direction: 'horizontal',
        loop: true,
        // 头部分页器
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            autoplay:true,
            loop:true,
            renderBullet: function (index, className) {
                return '<span class="re ' + className + '"><em></em></span>';
            },
        },

        // 前进后退按钮
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    })
    //室内灯饰轮播
    var mySwiper = new Swiper('.lightShow', {
        slidesPerView: 1,
        slidesPerGroup: 1,
        centeredSlides: true,
        direction: 'horizontal',
        loop: true,
        // 头部分页器
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: function (index, className) {
                return '<span class="re ' + className + '"><em></em></span>';
            },
        },

        // 前进后退按钮
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    })
    //产品介绍轮播
    var swiper = new Swiper('.introLun', {
        slidesPerView: 1,
        slidesPerGroup: 1,
        centeredSlides: true,
        direction: 'horizontal',
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: function (index, className) {
                return '<span class="re ' + className + '"><em></em></span>';
            },
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });


    //


    // 瀑布框
    for (var i = 0; i < oBor.length; i++) {
        oBor[i].style.width = oImg[i].clientWidth - 36 + 'px';
        oBor[i].style.height = oImg[i].clientHeight - 48 + 'px';
    }
}





// jquery
$(document).ready(function () {
    // 头部下滑
    $(document).scroll(function () {
        var top = $(document).scrollTop();
        if (top > 0) {
            $('#logo').css({
                transform: 'scale(0.8)'
            })
            $('#topNav').css({
                backgroundColor: '#000',
                height: '66px'
            })
            $('#ul li').css({
                paddingTop: '23px',
                paddingBottom: '23px'
            });
        }
        if (top == 0) {
            $('#topNav').css({
                height: '80px',
                backgroundColor: 'rgba(255,255,255,0)'
            })
            $('#ul li').css({
                paddingTop: '30px',
                paddingBottom: '30px'
            });
            $('#logo').css({
                transform: 'scale(1.0)'
            })
        }
    })
    // 商品换页
    for (let i = 0; i < $('.changePoint .changeDemo').length; i++) {
        // console.log($('goods'));
        $($('.goods')[i]).css({
            display: 'none'
        })
        $($('.goods')[0]).css({
            display: 'block'
        })
        //自动切换图片
        //   $('.photo img')[1].setAttribute('src',$('.photo img')[0].src)
        // 默认第一张商品表的小图为第二张商品表的大图
        $('.change img')[0].setAttribute('src', $('.photo img')[1].src);

        $($('.changePoint .changeDemo')[i]).click(function () {
            if (i < $('.changePoint .changeDemo').length - 1) {
                $('.change img')[i].setAttribute('src', $('.photo img')[i + 1].src);
            }
            console.log(i);
            if (i == $('.changePoint .changeDemo').length - 1) {
                $('.change img')[$('.changePoint .changeDemo').length - 1].setAttribute('src', $('.photo img')[0].src);
            }
            for (var j = 0; j < $('.changePoint .changeDemo').length; j++) {
                $($('.goods')[j]).css({
                    // opacity:'0',
                    display: 'none'
                })
                $($('.goods')[j]).stop(true).animate({
                    opacity: '0'
                }, 800)
            }
            $($('.goods')[i]).css({
                // opacity:'1',
                display: 'block'
            })
            $($('.goods')[i]).stop(true).animate({
                opacity: '1'
            }, 800)
        })
    }


    //瀑布处动画
    for (let i = 0; i < $('.riverBox .imgBox').length; i++) {
        $($('.riverBox .imgBox')[i]).hover(function () {
            console.log(i)
            $($('.bor')[i]).css({
                border: '3px solid #f2cb62',
                transform: 'scale(1.0)',
                transition: '0.5s'
            })
            $($('.text')[i]).css({
                bottom: '70px',
                left: '40px',
                opacity: '1',
                transition: '0.5s'
            })
            $($('.text .title')[i]).css({
                color: '#f2cb62',
                paddingBottom: '5px',
                transition: '0.5s'
            })
            $($('.riverBox .More')[i]).css({
                bottom: '45px',
                opacity: '1',
                transition: '0.5s'
            })
        }, function () {
            $($('.text .title')[i]).css({
                color: '#999',
                paddingBottom: '0px',
                transition: '0.5s'
            })
            $($('.text')[i]).css({
                bottom: '20px',
                left: '40px',
                opacity: '0',
                transition: '0.5s'
            })
            $($('.bor')[i]).css({
                border: '3px solid #000',
                transform: 'scale(1.06)',
                transition: '0.5s'
            })
            $($('.riverBox .More')[i]).css({
                bottom: '60px',
                opacity: '0',
                transition: '0.5s'
            })
        })
    }
    //瀑布布局
    $('#box').imagesLoaded(function () {
        // images have loaded
        $('.grid').masonry({
            // options
            itemSelector: '.grid-item',
            columnWidth: 432,
        });
    });
    //创新灯具轮播生成数字
    for (let i = 0; i < $('.shopGo .demo').length; i++) {
        $($('.shopGo .sNumber')[i]).text('0' + (parseInt(i) + 1));
    }

    //数字滚动
    $(document).ready(function () {
        $('.counter').countUp({
            delay: 1,
            time: 500
        });
    })
})