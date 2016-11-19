/**
 * js的两个基本函数
 * Update time：2014-8-12 15:39:37
 */

// 获取元素在页面中的位置
function l_t(elem) {
	 var arr = new Array();
	 var t = elem.offsetTop;
	 var l = elem.offsetLeft;
	 while( elem = elem.offsetParent) 
	 {  
	      t += elem.offsetTop;
	      l += elem.offsetLeft;
	 }
	 arr[0] = t;
	 arr[1] = l;
	 return arr;
}

function getByclass (className) {

	var tagname = arguments[1] ? arguments[1] : '*';		// 这个太爽了，js版的php默认参数实现方法哈
	var tag_list=document.getElementsByTagName(tagname);  	// 选中父节点下所有元素
    var i=0;
    var l = tag_list.length;
    var aResult=[];

    for(; i<l; i++) {

		// 如果存在空格分割那么则说明使用了多个类名
		if (tag_list[i].className.indexOf(' ') > 0) {
			var tag_list_ = tag_list[i].className.split(/\s+/);
			var l_ = tag_list_.length;
			// alert(tag_list[i].className);
			// 这种方法兼容多类名的元素（完美）
			for (var i_ = 0; i_ < l_; i_++) {
		    	if(tag_list_[i_] == className) {
		        	aResult.push(tag_list[i]);   //将选出的所有元素装入数组中
		        	i++;
		        }
	    	};
		}

        if(tag_list[i].className == className) {
        	aResult.push(tag_list[i]);   //将选出的所有元素装入数组中
        }
    }
    return aResult;
}