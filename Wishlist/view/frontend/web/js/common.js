
    require(['jquery'],function($){
        $(document).ready(function() {
        	   
        	
        	$('.recommend-bxslider').bxSlider({
                slideWidth:315,
                minSlides: 2,
                maxSlides: 4,
                moveSlides: 1,
                slideMargin:20,
                infiniteLoop: false,
                pager:0
            });
            $('.homebanner-bx').bxSlider({
                pagerCustom: '#bx-pager',
                useCSS: false,
                auto: true,
                pause:7000,
                infiniteLoop: true
            });
            /*Home page slider end*/

            $('.bxslider').bxSlider({
                slideWidth:315,
                minSlides: 2,
                maxSlides: 4,
                moveSlides: 1,
                slideMargin:20,
                // infiniteLoop: false,
                pager:0
            });
            $('.mobilepdpslider').bxSlider({
                minSlides:1,
                maxSlides:1,
                moveSlides: 1,
                // infiniteLoop: false,
                pager:1
            });	
        	
        	

//Clearfix common function start
function prodList(classname,indexmodule) {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    $('.' + classname).css('clear', 'none');
    if (width > 768) {
        if (indexmodule == undefined) {
            indexmodule = 4;
        }
        $('.' + classname).each(function (index, item) {
            if ((index + parseInt(indexmodule)) % parseInt(indexmodule) == 0) {
                $(item).css('clear', 'both');
            }
        });
    }
    else {
        if (indexmodule == undefined) {
            indexmodule = 3;
        }
        $('.' + classname).each(function (index, item) {
            if ((index + parseInt(indexmodule)) % parseInt(indexmodule) == 0) {
                $(item).css('clear', 'both');
            }
        });
    }
}
        });
    });
//Clearfix common function end