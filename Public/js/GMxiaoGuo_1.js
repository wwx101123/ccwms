(function($) {
	//商品分类导航开始

	$(function() {
		$(".ulNav .liNav").hover(function() {
			$('.navContent').css('display', 'none');
			$(this).addClass('addLiNav').siblings().removeClass('addLiNav');
			$(this).find('.navContent').css('display', 'block');
		}, function() {
			$(".ulNav .liNav").removeClass('addLiNav');
			$('.navContent').css('display', 'none');
		})
	});


	$.fn.objZoom = function(options) {
		var settings = {
			xzoom: 200,
			yzoom: 200,
			offset: 10,
			position: "right",
			lens: 1,
			preload: 1
		};
		if (options) {
			$.extend(settings, options)
		}
		var noalt = '';
		$(this).hover(function() {
				var imageLeft = $(this).offset().left;
				var imageTop = $(this).offset().top;
				var imageWidth = $(this).children('img').get(0).offsetWidth;
				var imageHeight = $(this).children('img').get(0).offsetHeight;
				noalt = $(this).children("img").attr("alt");
				var bigimage = $(this).children("img").attr("jqimg");
				$(this).children("img").attr("alt", '');
				if ($("div.zoomdiv").get().length == 0) {
					$(this).after("<div class='zoomdiv'><img class='bigimg' src='" + bigimage + "'/></div>");
					$(this).append("<div class='jqZoomPup'>&nbsp;</div>")
				}
				if (settings.position == "right") {
					if (imageLeft + imageWidth + settings.offset + settings.xzoom > screen.width) {
						leftpos = imageLeft - settings.offset - settings.xzoom
					} else {
						//leftpos = imageLeft + imageWidth + settings.offset
						leftpos = imageWidth + settings.offset;

					}
				} else {
					leftpos = imageLeft - settings.xzoom - settings.offset;
					if (leftpos < 0) {
						leftpos = imageLeft + imageWidth + settings.offset
					}
				}
				$("div.zoomdiv").css({
					top: 1, //imageTop,
					left: leftpos
				});
				$("div.zoomdiv").width(settings.xzoom);
				$("div.zoomdiv").height(settings.yzoom);
				$("div.zoomdiv").show();
				if (!settings.lens) {
					$(this).css('cursor', 'crosshair')
				}
				$(document.body).mousemove(function(e) {
					mouse = new MouseEvent(e);
					var bigwidth = $(".bigimg").get(0).offsetWidth;
					var bigheight = $(".bigimg").get(0).offsetHeight;
					var scaley = 'x';
					var scalex = 'y';
					if (isNaN(scalex) | isNaN(scaley)) {
						var scalex = (bigwidth / imageWidth);
						var scaley = (bigheight / imageHeight);
						$("div.jqZoomPup").width((settings.xzoom) / scalex);
						$("div.jqZoomPup").height((settings.yzoom) / scaley);
						if (settings.lens) {
							$("div.jqZoomPup").css('visibility', 'visible')
						}
					}
					xpos = mouse.x - $("div.jqZoomPup").width() / 2 - imageLeft;
					ypos = mouse.y - $("div.jqZoomPup").height() / 2 - imageTop;
					if (settings.lens) {
						xpos = (mouse.x - $("div.jqZoomPup").width() / 2 < imageLeft) ? 0 : (mouse.x + $("div.jqZoomPup").width() / 2 > imageWidth + imageLeft) ? (imageWidth - $("div.jqZoomPup").width() - 2) : xpos;
						ypos = (mouse.y - $("div.jqZoomPup").height() / 2 < imageTop) ? 0 : (mouse.y + $("div.jqZoomPup").height() / 2 > imageHeight + imageTop) ? (imageHeight - $("div.jqZoomPup").height() - 2) : ypos
					}
					if (settings.lens) {
						$("div.jqZoomPup").css({
							top: ypos,
							left: xpos
						})
					}
					scrolly = ypos;
					$("div.zoomdiv").get(0).scrollTop = scrolly * scaley;
					scrollx = xpos;
					$("div.zoomdiv").get(0).scrollLeft = (scrollx) * scalex
				})
			},
			function() {
				$(this).children("img").attr("alt", noalt);
				$(document.body).unbind("mousemove");
				if (settings.lens) {
					$("div.jqZoomPup").remove()
				}
				$("div.zoomdiv").remove()
			});
		count = 0;
		if (settings.preload) {
			$('body').append("<div style='display:none;' class='jqPreload" + count + "'>sdsdssdsd</div>");
			$(this).each(function() {
				var imagetopreload = $(this).children("img").attr("jqimg");
				var content = jQuery('div.jqPreload' + count + '').html();
				jQuery('div.jqPreload' + count + '').html(content + '<img src=\"' + imagetopreload + '\">')
			})
		}
	}
})(jQuery);

function MouseEvent(e) {
	this.x = e.pageX;
	this.y = e.pageY
}
$(document).ready(function() {
	//	event.stopPropagation();

	$(".xiaoGuo").objZoom({
		xzoom: 400,
		yzoom: 400,
		offset: 1,
		position: "right",
		preload: 1,
		lens: 1
	});
});

//缩略图切换
$('.xiaoTuList').each(function(i, o) {
	var lilength = $('.xiaoTuList').length;
	$(o).hover(function() {
		$(o).siblings().removeClass('active');
		$(o).addClass('active');
		$('#zoomimg').attr('src', $(o).find('img').attr('data-img'));
		$('#zoomimg').attr('jqimg', $(o).find('img').attr('data-big'));
		$('.next-btn').removeClass('disabled');
		if (i == 0) {
			$('.next-left').addClass('disabled');
		}
		if (i + 1 == lilength) {
			$('.next-right').addClass('disabled');
		}
	});
})


//购买数量-1
var store_count = 100;
$('.jianS').click(function() {
	var buynum = parseInt($('.shuTextN').val());
	if (buynum > 1) {
		$('.shuTextN').val(buynum - 1);
	}
	if (buynum - 1 == 1) {
		$('.jianS').addClass('noJianS');
	}
	$('.add').removeClass('noJianS');
	return false;
});

//购买数量+1
$('.add').click(function() {
	var buynum = parseInt($('.shuTextN').val());
	if (buynum < store_count) {
		$('.shuTextN').val(buynum + 1);
	}
	if (buynum + 1 == store_count) {
		$('.add').addClass('noJianS');
	}
	$('.jianS').removeClass('noJianS');
	return false;
});


$(document).ready(function() {
	$("#shouJi").mouseover(function() {
		$("#shouJiTan").show();
	});
	$("#shouJi").mouseleave(function() {
		$("#shouJiTan").hide();
	});
});




//规格属性
$('.SpecifiLinkAF .SpecifiLinkA').click(function() {
	$(this).addClass('SpecifiLinkABG').siblings().removeClass('SpecifiLinkABG');
});



/*商品介绍、规格参数、商品评价、售后保障*/
$('.gmXQBTRightTitle li').each(function(index, obj) {
	$(obj).click(function() {
		$(obj).addClass('addBGLi');
		$(obj).siblings().removeClass('addBGLi');
	})

});


