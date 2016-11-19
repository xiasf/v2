/**
 * 编辑商店 js功能
 * update：2016-4-19 14:48:27
 */

 $(function(){

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

    new Switchery(document.querySelector(".js-switch"), {color: "#1AB394"});
    $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});

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
    if ($('input[name=is_seller_settlement]:checked').val() == 1) {
        $('input[name=settlement_account]').attr('readonly', true);
        $('input[name=bank_name]').attr('readonly', true);
        $('input[name=bank_user_name]').attr('readonly', true);
        $('input[name=bank_card]').attr('readonly', true);
    }

    var map = new BMap.Map("milkMap");              // 创建地图实例
    var point = new BMap.Point(shop_data.lng, shop_data.lat);  // 创建点坐标
    map.centerAndZoom(point, 14);                 // 初始化地图，设置中心点坐标和地图级别
    // var myIcon = new BMap.Icon("http://www.iconpng.com/png/mapmarkers/bubble_pink.png", new BMap.Size(300,157));

    creMarker(point);

    function creMarker(point) {
        marker = new BMap.Marker(point);        // 创建标注  
    　　map.addOverlay(marker);                 // 将标注添加到地图中
        marker.enableDragging();
        marker.addEventListener("dragend", coordinates);
    }


    function showInfo(e) {
        map.removeOverlay(marker);
        var point =new BMap.Point(e.point.lng, e.point.lat);
        creMarker(point);
        
        coordinates(e);
    }
    map.addEventListener("click", showInfo);

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


    $("form[name=shopForm]").submit(function(){
        return true;
        $.post("{:U('update')}", $(this).serialize(), function(result){
            if (result.status == 1) {
                alert(result.info);
                window.location.href = result.url;
            } else {
                alert(result.info);
            }
        }, 'json');
        return false;
    });
});