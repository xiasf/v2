/**
 * 购物车基础
 * update：2016-4-2 22:08:25
 */

/**
 * 检测购物车cookie是否合法
 * @return  void
 */
function check_cart_cookie() {
	if ($.cookie('cart')) {
		var reg = /^(\d+\.\d+-)*(\d+\.\d+)$/;
		if (reg.test($.cookie('cart')))
			return true;
		else {
			layer.open({
				btn: ['你逗我呢'],
				title: '抱歉，购物车数据异常！',
				content: 'error：'+$.cookie('cart')+'<br /><br />如果您遇到了此问题请及时报告我们，谢谢！'
			});
			// set_cookie('cart', null);
			// window.location.reload();
		}
	}
}


/**
 * cookie版购物车设置
 * @param   int   	 id 	目标菜单id
  * @param   int   	 num 	数量
 * @return  void
 */
function set_cart_cookie(id, num) {
	var reg = new RegExp(id + '\\.\\d+');
	var cart = $.cookie('cart');
	if (0 >= num) {
		del_products(id);	// 删除
	} else if (!cart) {
		set_cookie('cart', id + '.' + num);	// 首次点餐
	} else if (reg.test(cart)) {
		set_cookie('cart', cart.replace(reg, id + '.' + num));	// 已点这个
	} else {
		set_cookie('cart', (cart + '-' +  id + '.' + num).replace(/^-+|-+$/g, ''));	// 首次点这个
	}
	check_cart_cookie();
}


/**
 * 根据菜单id删除某个菜品
 * @param   int    id
 * @return  void
 */
function del_products(id) {
	var reg = new RegExp('(-)?'+id+'\\.\\d+(-)?');
	var cart = $.cookie('cart');
	if (reg.test(cart)) {
		set_cookie('cart', cart.replace(reg, ((RegExp.$1 + RegExp.$2) == '--') ? '-' : ''), 7);
		check_cart_cookie();
	}
}