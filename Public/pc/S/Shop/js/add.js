/**
 * 添加商店 js功能
 * update：2016-4-19 14:48:27
 */

 $(function(){
    new Switchery(document.querySelector(".js-switch"), {color: "#1AB394"});
    $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});

    $("#filePicker_logo").uploader({
        server:img_upload_url + '?file=logo',
        filelist:"#fileList_logo",
        fileVal:'logo',
        uploaded:function(result){
            $("input[name='logo']").val(result.url.slice(1));
        },
    });

    $("#filePicker_bitmap").uploader({
        server:img_upload_url + '?file=store_bitmap',
        filelist:"#fileList_bitmap",
        fileVal:'store_bitmap',
        uploaded:function(result){
            $("input[name='store_bitmap']").val(result.url.slice(1));
        },
    });


    var map = new BMap.Map("milkMap");              // 创建地图实例
    var point = new BMap.Point(114.880453, 30.465399);  // 创建点坐标
    map.centerAndZoom(point, 14);                 // 初始化地图，设置中心点坐标和地图级别
    // var myIcon = new BMap.Icon("http://www.iconpng.com/png/mapmarkers/bubble_pink.png", new BMap.Size(300,157));

    creMarker(point);

    function creMarker(point) {
        marker = new BMap.Marker(point);        // 创建标注  
    　　map.addOverlay(marker);                 // 将标注添加到地图中
        marker.enableDragging();
    }

    function showInfo(e) {
        map.removeOverlay(marker);
        var point =new BMap.Point(e.point.lng, e.point.lat);
        creMarker(point);
        coordinates(e);
    }

    // 单击事件
    map.addEventListener("click", showInfo);

    marker.addEventListener("dragend", coordinates);

    // 移动地图时的事件
    map.addEventListener('ondragging', function(e){
        marker.setPosition(map.getCenter());
        // 直接用这个可能会导致页面卡主html冗积太多
        // coordinates(e);
        $('input[name=lng]').val(e.point.lng);
        $('input[name=lat]').val(e.point.lat);
    });

    // 移动地图完成的事件
    map.addEventListener('dragend', function(e){
        marker.setPosition(map.getCenter());
        coordinates(e);
    });

    function coordinates(e) {
        var p = marker.getPosition();
        map.panTo(p);
        var geo = new BMap.Geocoder();
        geo.getLocation(p, function(addr){
            $('input[name=address]').val(addr.address);
            var address_reference = addr.surroundingPois[0] ? addr.surroundingPois[0]['title'] : '';
            $('input[name=address_reference]').val(address_reference);
        });

        $('input[name=lng]').val(e.point.lng);
        $('input[name=lat]').val(e.point.lat);
    }


      // 添加带有定位的导航控件
      var navigationControl = new BMap.NavigationControl({
        // 靠左上角位置
        anchor: BMAP_ANCHOR_TOP_LEFT,
        // LARGE类型
        type: BMAP_NAVIGATION_CONTROL_LARGE,
        // 启用显示定位
        enableGeolocation: true
      });
      map.addControl(navigationControl);
      // 添加定位控件
      var geolocationControl = new BMap.GeolocationControl();
      geolocationControl.addEventListener("locationSuccess", function(e){
        // 定位成功事件
        var address = '';
        address += e.addressComponent.province;
        address += e.addressComponent.city;
        address += e.addressComponent.district;
        address += e.addressComponent.street;
        address += e.addressComponent.streetNumber;
        alert("当前定位地址为：" + address);
      });
      geolocationControl.addEventListener("locationError",function(e){
            // 定位失败事件
            alert(e.message);
      });
      map.addControl(geolocationControl);


    map.enableScrollWheelZoom();
    map.enableInertialDragging();

    map.enableContinuousZoom();

    var size = new BMap.Size(60, 20);
    map.addControl(new BMap.CityListControl({
        anchor: BMAP_ANCHOR_TOP_LEFT,
        offset: size,
        // 切换城市之间事件
        // onChangeBefore: function(){
        //    alert('before');
        // },
        // 切换城市之后事件
        // onChangeAfter:function(){
        //   alert('after');
        // }
    }));

    $('input[name=is_seller_settlement]').on('ifChecked', function(){
        if ($(this).val() == 1) {
            $('input[name=settlement_account]').attr('readonly', true);
            $('input[name=bank_name]').attr('readonly', true);
            $('input[name=bank_user_name]').attr('readonly', true);
            $('input[name=bank_card]').attr('readonly', true);
        } else {
            $('input[name=settlement_account]').attr('readonly', false);
            $('input[name=bank_name]').attr('readonly', false);
            $('input[name=bank_user_name]').attr('readonly', false);
            $('input[name=bank_card]').attr('readonly', false);
        }
    });

    $('select[name=shop_business_type]').on('change', function(){
        if ($(this).val() == 1) {
            $('input[name=seller_id]').val('');
            $('#seller_name').attr('disabled', true);
            $('#seller_name').val('自营店铺由平台管理');
            $('#seller_name').parent().find('button').attr('disabled', true);
        } else {
            $('#seller_name').val('');
            $('#seller_name').attr('disabled', false);
            $('#seller_name').parent().find('button').attr('disabled', false);
        }
    });
    // $('select[name=shop_business_type]').trigger('change');
    // $('select[name=shop_business_type] option[value=1]').attr('selected', true);

    var baiduBsSuggest = $("#seller_name").bsSuggest({
        indexId: 2,
        indexKey: 1,
        keyField:'name',
        allowNoKeyword: true,
        multiWord: true,
        separator: ",",
        getDataMethod: "url",
        effectiveFieldsAlias: {
            Id: "序号",
            Keyword: "商户名称",
            Count: "ID"
        },
        showHeader: true,
        inputWarnColor: "rgba(255,0,0,.1)",
        url: get_seller_url + '?' + Math.random() + "&name=",
        jsonp: null,
        processData: function(json) {
            var i, len, data = {
                value: []
            };
            if (!json || json.length === 0) {
                return data;
            }
            len = json.length;
            for (i = 0; i < len; i++) {
                data.value.push({
                    "Id": i + 1,
                    "Keyword": json[i]['seller_name'],
                    "Count": json[i]['id']
                });
            }
            return data;
        }
    });
    $("#seller_name").on('onSetSelectValue', function (e, Keyword) {
        $('input[name=seller_id]').attr('data-value', Keyword.id);
        $('input[name=seller_id]').val(Keyword.id);
        if ($('#seller_name').val() != '' && $('input[name=seller_id]').val() != '') {
            // init_cat_list(Keyword.id);
        }
    }).on("onDataRequestSuccess", function (e, result) {
        $('input[name=seller_id]').val('');
        if (!result) {
            $('input[name=seller_id]').val('');
        } else {
            $('input[name=seller_id]').val($('input[name=seller_id]').attr('data-value'));
        }
    }).on('onUnsetSelectValue', function (e) {
        console.log("onUnsetSelectValue");
    });

    $('select[name=shop_business_type]').trigger('change');


    $("form[name=shopForm]").submit(function(){
        // return true;

        // 防止意外发生
        if (($('select[name=shop_business_type]').val() == 2 && $('input[name=seller_id]').val() == '') || ($('#seller_name').val() == '' && $('input[name=seller_id]').val() != '')) {
            alert('请选择商户');
            $('#seller_name').trigger('focus');
            return false;
        }

        $.post("{:U('add')}", $(this).serialize(), function(result){
            if (result.status == 1) {
                alert(result.info);
                window.location.href = result.url;
            } else {
                alert(result.info);
            }
        });
        return false;
        // 不采用令牌的方案无法从根本上解决重复提交（多次提交）的问题，也无法阻止攻击者通过蓄意构造的欺诈请求的攻击
    });
});