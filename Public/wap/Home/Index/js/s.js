/**
 * 首页选择类目js功能
 * update：2016-4-4 11:34:14
 */

$(function() {
	$('.cat').bind('click', function(){
		set_cookie('category_id', $(this).data('category-id'));
		window.location.href = area_url;
	});
});