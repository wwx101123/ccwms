//头部隐藏与显示
$(function () {
    var $bdy = $('body');
    var $container = $('#pgcontainer');
    var $burger = $('#Display');
    var menuwidth = 150;
    var menuspeed = 300;
    var negwidth = menuwidth + "px";
    var poswidth = "-" + menuwidth + "px";
    $('.zoombtn').on('click', function (e) {
        if ($bdy.hasClass('Cover')) {
            jsAnimateMenu('close');
        } else {
            jsAnimateMenu('open');
        }
    });
    $('.Film').on('click', function (e) {
        if ($bdy.hasClass('Cover')) {
            jsAnimateMenu('close');
        }
    });
    function jsAnimateMenu(tog) {
        if (tog == 'open') {
            $bdy.addClass('Cover');
            $container.animate({marginRight: negwidth, marginLeft: poswidth}, menuspeed);
            $burger.animate({width: negwidth}, menuspeed);
            $('.Film').animate({right: negwidth}, menuspeed);
        }
        if (tog == 'close') {
            $bdy.removeClass('Cover');
            $container.animate({marginRight: "0", marginLeft: "0"}, menuspeed);
            $burger.animate({width: "0"}, menuspeed);
            $('.Film').animate({right: "0"}, menuspeed);
        }
    }
});