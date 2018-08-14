//处理点击this事件，需要打开原生浏览器
//mui('body').on('tap', 'a', function(e) {
//	var href = this.getAttribute('href');
//	if(href) {
//		if(window.plus) {
//				plus.runtime.openURL(href);
//		} else {
//			location.href = href;
//		}
//	}
//});
mui('body').on('tap', 'a', function(e) {
	var href = this.getAttribute('href');
	var reg = /\.html?/;
	var http = reg.test(href);
	if(http) {
		mui.openWindow({
			url: href,
			id: href,
			styles: {
				top: 0, //新页面顶部位置
				bottom: 0, //新页面底部位置
			},
			createNew: false, //是否重复创建同样id的webview，默认为false:不重复创建，直接显示
			show: {
				autoShow: true, //页面loaded事件发生后自动显示，默认为true
				duration: 300, //页面动画持续时间，Android平台默认100毫秒，iOS平台默认200毫秒；
			},
			waiting: {
				autoShow: true, //自动显示等待框，默认为true
				title: '正在加载...', //等待对话框上显示的提示内容
				options: {
					width: 100, //等待框背景区域宽度，默认根据内容自动计算合适宽度
					height: 100, //等待框背景区域高度，默认根据内容自动计算合适高度

				}
			}
		})
	}
});

//退出
mui('body').on('tap', ".logout", function () {
    var btnArray = ['确认', '取消'];
    mui.confirm('是否确认退出？', '温馨提示', btnArray, function (e) {
        if (e.index == 0) {
            mui.openWindow({
                id: 'logout',
                url: "/User/logout"
            });
        }
    });
});

//边侧栏导航guanbicbl
mui('body').on('tap', ".PopupNavigation", function () {
	$('.leftNavigationBox').show();
	if($('.leftNavigationBox').css('display') == 'block'){
        $('.leftNavigation').addClass('leftNavigationFocus');
	}
});
mui('body').on('tap', ".CloseButton", function () {
    $('.leftNavigationBox').hide();
    $('.leftNavigation').removeClass('leftNavigationFocus');
});
mui('body').on('tap', ".guanbicbl", function () {
    $('.leftNavigationBox').hide();
    $('.leftNavigation').removeClass('leftNavigationFocus');
});



mui('body').on('tap', ".navigTop", function () {
    if($(this).next('.navigTxHei').css('display') == 'none'){
        $('.navigTxHei').hide();
        $('.zhiyou').show();
        $('.zhixia').hide();
        $(this).next('.navigTxHei').show();
        $(this).find('.zhixia').show();
        $(this).find('.zhiyou').hide();
	}else {
        $(this).next('.navigTxHei').hide();
        $(this).find('.zhiyou').show();
        $(this).find('.zhixia').hide();
	}
});


//滚动消息
function AutoScroll(obj) {
	$(obj).find("ul:first").animate({
			marginTop: "-35px"
		},
		800,
		function() {
			$(this).css({
				marginTop: "0px"
			}).find("li:first").appendTo(this);
		});
}
$(document).ready(function() {
	setInterval('AutoScroll("#demoGd")', 2500)
});
//返回顶部
$(function() {
	$('#fhui').hide();
	$(window).scroll(function() {
		if($(this).scrollTop() > 100) {
			$('#fhui').stop().fadeIn();
		} else {
			$('#fhui').stop().fadeOut();
		}
	});
	mui('#fhui').on('tap','span',function() {
		$(window).scrollTop({
			scrollTop: 0
		},300);
		return false
	});
});






// 页面跳转
mui("body").on('tap','.likeA',function(){
    //获取id
    var url = this.getAttribute("data-url");
	if(url != null) {
        mui.openWindow({
            id:url,
            url:url
        });
	}
});
mui("body").on('tap','.reminderByNo',function(){
	return mui.alert($(this).data('tishi'),'温馨提示');
});
mui.plusReady(function(){
 	plus.geolocation.getCurrentPosition(function(p){
		mui.alert('Geolocation\nLatitude:' + p.coords.latitude + '\nLongitude:' + p.coords.longitude + '\nAltitude:' + p.coords.altitude);
	}, function(e){
		mui.alert('Geolocation error: ' + e.message);
	} );
});


mui('body').on('tap',".clickCyjy",function() {
    var Cyjy = $(this).next('.cyjyBox').css('display');
    if (Cyjy == ('none')) {
        $(this).next('.cyjyBox').css('display','block');
    } else{
        $(this).next('.cyjyBox').css('display','none');
    }
});
