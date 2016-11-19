/**
 * 选择餐品js功能
 * update：2016-4-16 09:02:22
 */


/**
 * 渲染产品分类列表
 * @param   obj   	 result 	分类数据
 * @return  void
 */
var tpl_cat_list = function(result) {
	var html = template('tpl_cat_list', {'cat_list':result});
	$('#cat_list').html(html);
	$('#cat_list').data('load', 1);
	check_load();

	// 绑定事件
};

/**
 * 渲染产品列表
 * @param   obj   	 result 	产品数据
 * @return  void
 */
var tpl_products_list = function(result) {
	var html = template('tpl_products_list', {'products_list':result});
	$('#products_list').html(html);
	$('#products_list').data('load', 1);
	check_load();
}

/**
 * 渲染购物车
 * @param   obj   	 result 	购物车数据
 * @return  void
 */
var tpl_cart = function(result) {
	var html = template('tpl_cart', {'cart':result});
	$('#cart').html(html);
	$('#cart').data({load: 1, cart_data: result});
	check_load();
	if (0 == result.num)
		$('#cart_botton').html('空的哦');
	else {
		$('#cart_botton').html('选好了');
		$('#cart_botton').addClass('ok');
	}
}


/**
 * 初始化产品列表区域按钮
 * @param   obj   	 cart_data 	购物车数据
 * @return  void
 */
function init_btn(cart_data) {
	$('#products_list li').each(function() {
		var id = $(this).data('id');
		$(this).find('button.inc').removeClass('hide');
		if (cart_data.list[id]) {
			$(this).find('input.num').val(cart_data.list[id]['num']).removeClass('hide');
			$(this).find('button.dec').removeClass('hide');
		}
	});
	bind_btn();		// 为按钮绑定事件
}


// 为产品列表区域按钮绑定事件
function bind_btn() {
	$('#products_list').find('input,button').click(function() {
		if ($(this).is('.num')) {
		}
		if ($(this).is('.dec')) {
			cart_reduction($(this));
		}
		if ($(this).is('.inc')) {
			cart_add($(this));
		}
	});
}


/**
 * 产品列表区域 减按钮
 * @param   obj   	 button 		按钮对象
 * @param   num   	 change_num 	改变量，默认为1
 * @return  void
 */
function cart_reduction(button, change_num) {
	if (button.is('.active')) return;

	var id = parseInt(button.parent().parent().data('id'));
	var num = parseInt(button.parent().find('.num').val());	// 当前数量
	change_num = -(change_num ? change_num : 1);	// 要改变的量（负数代表减少）

	num = num + change_num;

	// 等待服务器响应前的样式
	button.addClass('active');

	cart_effect_Por(button, num, change_num);

	set_cart_cookie(id, num);
  	button.removeClass('active');
}


/**
 * 产品列表区域 加按钮
 * @param   obj   	 button 		按钮对象
 * @param   num   	 change_num 	改变量，默认为1
 * @return  void
 */
function cart_add(button, change_num) {
	if (button.is('.active')) return;

	var id = parseInt(button.parent().parent().data('id'));
	var num = parseInt(button.prev().prev().val());	// 当前数量

	change_num = change_num ? change_num : 1;	// 要改变的量（负数代表减少）

	num = !num ? 1 : num + change_num;

	// 等待服务器响应前的样式
	button.addClass('active');

	// 完成cookie操作和视图操作
	switch(cart_scenario_check(id)) {
		case 0: 								// 用户首次点餐
		  	cart_effect_Por(button, num, change_num);
		  	break;
		case 1: 								// 首次点这个
		  	cart_effect_Por(button, num, change_num);
		  	break;
		case 2: 								// 已经点过这个
		  	cart_effect_Por(button, num, change_num);
		  	break;
	}
	set_cart_cookie(id, num);
  	button.removeClass('active');
}


/**
 * 页面信息更新
 *（cookie的更新非常简单，但是页面信息或者dom的更改非常复杂，极不易控制，如果引入vue.js这类的前端框架可能会很好的解决这个问题）
 * @param   obj   	 t 				按钮对象
 * @param   int   	 num 			数量
 * @param   int   	 change_num 	改变量
 * @return  void
 */
function cart_effect_Por(button, num, change_num) {
	var price = parseFloat(button.parent().prev().find('.price').html());
	var change_total = accMul(price, change_num);

	// 减少或者删除（按钮对象为减）
	if (num <= 0) {
		button.parent().find('.num').val(0).addClass('hide');
		button.parent().find('.dec').addClass('hide');
	} else {
		button.parent().find('.num').val(num);
		button.parent().find('.num').removeClass('hide');
		button.parent().find('.dec').removeClass('hide');
	}

	$('#cart_num').html(parseInt($('#cart_num').html()) + change_num);
	$('#cart_total').html(accAdd(parseFloat($('#cart_total').html()), change_total));

	shop_critical();
}


/**
 * 下单情景检测
 * @param   int   	 cid 	目标餐厅id
 * @return 	int 	 id     目标菜单id
 * @return  void
 */
function cart_scenario_check(id) {
	var scenario;
 	var cart = $.cookie('cart');
 	if (cart) {
 		if (cart.indexOf(id + '.') != -1) {
 			// 已经点过这个
			scenario = 2;
 		} else {
 			// 首次点这个
 			scenario = 1;
 		}
 	} else {
 		// 首次点餐
 		scenario = 0;
 	}
 	return scenario;
}


// 下单检查（起送价等检查）
function shop_critical() {
	if (parseInt($('#cart_num').html())) {
		$('#cart_botton').html('选好了');
		$('#cart_botton').addClass('ok');
	} else {
		$('#cart_botton').html('空的哦');
		$('#cart_botton').removeClass('ok');
	}
}


// 下单按钮事件
function checkout() {
	if ($('#cart_botton').html() != '选好了') return;
	window.location.href = cart_checkout_url;
}

// 检测加载 这才是最科学的方法，因为我们无法预知异步会在何时完成，异步之间是并行的
function check_load() {
	var $cat_list = $('#cat_list');
	var $products_list = $('#products_list');
	var $cart = $('#cart');
	if (1 == $cat_list.data('load') && 1 == $products_list.data('load') && 1 == $cart.data('load')) {
		init_btn($cart.data('cart_data'));
		layer.close(load);	// 加载完成
	}
}


$(function() {
	var callback = [tpl_cat_list, tpl_products_list];
	get_products_json(callback);	// 获取产品数据并渲染（渲染分类列表，产品列表）

	// 购物车初始化渲染
	var callback2 = [tpl_cart];
	get_cart_json(callback2);	// 获取数据并渲染
});


/**
 * 获取产品信息
 * @param   obj   	 callback 	数据获取成功后的回调
 * @return  void
 */
function get_products_json(callback) {
	$.getJSON(get_products_json_url+'?'+Math.random(), function(result) {
		for (var i in callback) {
			callback[i](result);
		}
	});
}

/**
 * 获取产品信息
 * @param   obj   	 callback 	数据获取成功后的回调
 * @return  void
 */
function get_cart_json(callback) {
	$.getJSON(get_cart_json_url+'?'+Math.random(), {shop_id:$.cookie('shop_id')}, function(result) {
		for (var i in callback) {
			callback[i](result);
		}
	});
}