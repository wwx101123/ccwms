(function($){

$.fn.countdown=function(){
	var data="";
	var _DOM=null;
	var TIMER;
	createdom =function(dom){
            _DOM=dom;
            data=$(dom).attr("data");
            data = data.replace(/-/g,"/");
            data = Math.round((new Date(data)).getTime()/1000);
            $(_DOM).append("<span class='countdownday'></span><span class='split'>天</span><span class='countdownhour'></span><span class='split'>:</span><span class='countdownmin'></span><span class='split'>:</span><span class='countdownsec'></span>")
            reflash();
	};

	reflash=function(){

		var	range  	= data-Math.round((new Date()).getTime()/1000),
                    secday = 86400, sechour = 3600,
                    days 	= parseInt(range/secday),
                    hours	= parseInt((range%secday)/sechour),
                    min		= parseInt(((range%secday)%sechour)/60),
                    sec		= ((range%secday)%sechour)%60;
		if (sec < 0){
                    var id = $(_DOM).data('id');
			$(_DOM).html('');
                        miao_sj(id);
		}else{
			$(_DOM).find(".countdownday").html(days);
			$(_DOM).find(".countdownhour").html(nol(hours));
			$(_DOM).find(".countdownmin").html(nol(min));
			$(_DOM).find(".countdownsec").html(nol(sec));
		}
	};

	TIMER = setInterval( reflash,1000 );
	nol = function(h){
            return h>9?h:'0'+h;
	}
	return this.each(function(){
		var $box = $(this);
		createdom($box);
	});
}

})(jQuery);

$(function(){
    $(".countdownbox").each(function(){
        $(this).countdown();
    });
});

function miao_sj(id){
    $.ajax({
       type: 'post',
       url:'/index.php/Zfuwl/Api/tradeLocdAdd',
       data:{id:id},
       dataType:'json',
       success:function(data){
           if(data == 1){
                location.reload();
           }
       },
       error:function(data){
         location.reload();
       },
    });
}