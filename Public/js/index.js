layui.use(['carousel', 'laydate', 'element', 'form'], function() {
	var carousel = layui.carousel,
		form = layui.form;
	var $ = layui.jquery,
		element = layui.element;
	var laydate = layui.laydate;



	//尾部
	$('.phoneFooterList').click(function() {
		$(this).find('i').addClass('i');
		$(this).siblings().find('i').removeClass('i');
	})

});