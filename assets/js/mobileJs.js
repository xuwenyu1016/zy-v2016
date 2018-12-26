/**
 * Created by admin on 2017/9/6.
 */

//reset the form
function formReset() {
    document.getElementById("forSubmit").reset();
}
//reset the form

//baidu code
var _hmt = _hmt || []; (function() { var hm = document.createElement("script"); hm.src = "https://hm.baidu.com/hm.js?7705e9a0099666098aa67d7b57b53aff"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hm, s); })();
//baidu code

//form validation
$("#forSubmit").submit(function(){
    //alert("submitting...");
    var $name = $('input[name="name"]').val();
    var $tell = $('input[name="tell"]').val();
    var $verifiy = $('input[name="verifiy"]').val();
    var $content = $('textarea[name="content"]').val();

    var str = "";
    str += '  名字：';
    str += $name;
    str += '  手机号：';
    str += $tell;
    str += '  法律咨询：';
    str += $content;
//        alert(str);
    $('input[name="dizhi"]').val(str);
//        alert($('input[name="dizhi"]').val());

    if( $name == "" || $tell =="" || $verifiy == ""){
        alert('请填写相应信息！');
        return false;
    }else if (!$tell.match(/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
        alert('请正确填写您的手机号');
        $("this").focus().select();
        return false;
    }
});
//form validation

//acquire for text validation number
$(function () {
    //获取短信验证码
    var validCode = true;
    $(".msg").click (function () {
        $.ajax({
            cache: true,
            type: "POST",
            url: "/DuxCms/Form/miji_yz",
            data: $('#forSubmit').serialize(),// 你的formid
            async: false,
            error: function (request) {
                alert("系统出错请重新获取");
            },
            success: function (data) {
//                    alert(data);
//                     $("#commonLayout_appcreshi").parent().html(data);

//                    alert("申请成功,立即下载!");
//                    setTimeout(window.location.href="/mianshimiji/fudan.pdf",3000);
            }
        });

        var time = 60;
        var code = $(this);
        if (validCode) {
            validCode = false;
            code.addClass("msg");
            var t = setInterval(function () {
                time--;
                code.html('还剩余(' + time + ')秒');
                if (time == 0) {
                    clearInterval(t);
                    code.html("重新获取");
                    validCode = true;
                    code.removeClass("msg");

                }
            }, 1000)
        }
    })
});
//acquire for text validation number



//创建和初始化地图函数：
function initMap(){
    createMap();//创建地图
    setMapEvent();//设置地图事件
    addMapControl();//向地图添加控件
    addMapOverlay();//向地图添加覆盖物
}
function createMap(){
    map = new BMap.Map("map");
    map.centerAndZoom(new BMap.Point(121.472793,31.233918),19);
}
function setMapEvent(){
    map.enableDragging();
    map.enableDoubleClickZoom()
}
function addClickHandler(target,window){
    target.addEventListener("click",function(){
        target.openInfoWindow(window);
    });
}
function addMapOverlay(){
    var markers = [
        {content:"上海市静安区成都北路333号招商局广场南楼18楼",title:"震亚律所",imageOffset: {width:-46,height:-21},position:{lat:31.233926,lng:121.472735}}
    ];
    for(var index = 0; index < markers.length; index++ ){
        var point = new BMap.Point(markers[index].position.lng,markers[index].position.lat);
        var marker = new BMap.Marker(point,{icon:new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png",new BMap.Size(20,25),{
            imageOffset: new BMap.Size(markers[index].imageOffset.width,markers[index].imageOffset.height)
        })});
        var label = new BMap.Label(markers[index].title,{offset: new BMap.Size(25,5)});
        var opts = {
            width: 200,
            title: markers[index].title,
            enableMessage: false
        };
        var infoWindow = new BMap.InfoWindow(markers[index].content,opts);
        marker.setLabel(label);
        addClickHandler(marker,infoWindow);
        map.addOverlay(marker);
    };
}
//向地图添加控件
function addMapControl(){
    var scaleControl = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
    scaleControl.setUnit(BMAP_UNIT_IMPERIAL);
    map.addControl(scaleControl);
    var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:1});
    map.addControl(navControl);
    var overviewControl = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:false});
    map.addControl(overviewControl);
}
var map;
initMap();