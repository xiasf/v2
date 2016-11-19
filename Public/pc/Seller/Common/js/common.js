/**
 * 公用
 * update；2016-4-10 23:51:51
 */

// 配置
layer.config({
    extend:["extend/layer.ext.js", "skin/moon/style.css"],
    // skin:"layer-ext-moon",
    moveType: 1,
});

// 注册全局ajax失败控制
$.ajaxSetup({
    error:function(x, e) {
        top.layer.open({content: '抱歉，服务忙！'});
        return false;
    }
});


$(function(){
    
});