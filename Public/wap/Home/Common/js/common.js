/**
 * 公用
 * update；2016-4-10 23:51:51
 */

// 注册全局ajax失败控制
$.ajaxSetup({
    error:function(x, e) {
        layer.open({content: '抱歉，服务忙！'});
        return false;
    }
});


layer.mis = function (mis) {
	layer.open({
	    content: mis,
	    // style: 'background-color:#92192E; color:#fff; border:none;',
	    time: 1
	});
};


// cookie设置
function set_cookie(name, value) {
	if (value == null)
		$.cookie(name, value, {path:'/', expires:-1});
	else
		$.cookie(name, value, {path:'/', expires:7, secrue:false, raw: false});
}


$(function(){
	// 消除click延时
	FastClick.attach(document.body);

	$('#h_r,#sf_mask').bind('click', function(){
		$('#head_more').toggle();
		$('#h_r').toggleClass('active');
		$('#sf_mask').toggle();
	});

	// css3 calc的js替代方案
	$('.main_warp').css('height', parseInt($(window).height()) - 40 + 'px');
	$(window).resize(function(){
		$('.main_warp').css('height', parseInt($(window).height()) - 40 + 'px');
	});
});