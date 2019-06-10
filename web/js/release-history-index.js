$(function () {
    $.ajax({
        url: "/api/release-history/get-release-history",
        type: "get",
        // data:,
        success: function (data) {
            if (data.code==0){
                var  str=generateReleaseHistoryList(data.data);
                $('#ReleaseHistoryList').append(str);
            }else {
                alert("获取项目信息失败 code 不等于0"+data.message);
            }
        },
        error: function (err) {
            alert("直接error了"+err['responseJSON']['message']);
        }
    });
    $('#ReleaseHistoryList').on('click','.ReleaseHistory',function () {
        $('.ReleaseHistory').removeAttr('click');
        $('.ReleaseHistory').css('background-color','');
        //填充值
        $('.version').empty();
        $('.datetime').empty();
        var version=$(this).attr('version');
        var create_time=$(this).attr('create_time');
        $('.version').append(version);
        $('.datetime').append(create_time);
        $(this).attr('click','true');
        $(this).css('background-color','#9a9595');

    });

    $('#ReleaseHistoryList').on('mouseover','.ReleaseHistory',function () {
        if($(this).attr('click')!='true'){
        $(this).css('background-color','#9a9595');
        }
    });
    $('#ReleaseHistoryList').on('mouseout','.ReleaseHistory',function () {
        if($(this).attr('click')!='true') {
            $(this).css('background-color', '');
        }
    });
});

function generateReleaseHistoryList(data) {
    var  str='';
    var releaseStatus = {1:"普通发布", 2:"回滚"};


    for (let k in data) {
        var  time=formatDate(data[k]["create_time"]);
        str+='<div class="ReleaseHistory" style="border-right: #0f0f0f 4px ridge;height: 50px;cursor:pointer;" version='+data[k]["release_name"]+' create_time="'+[data[k]["create_time"]]+'"> <div style="padding-top: 5%"><div style="display: inline">'+data[k]["create_name"]+'</div><div id="releaseStatus">'+releaseStatus[data[k]["current_record_style"]]+'</div><div id="releaseDate">'+time+'</div></div></div> ';
    }
    return str;
}

 function formatDate(date) {
    date = date.substring(0,19);
    date = date.replace(/-/g,'/');
    date= new Date(date).getTime();

    var newTime = Date.parse(new Date());
    var interval = (newTime - date) / 1000;
    if (interval < 0) {
        return "刚刚";
    } else if (interval > 24 * 3600) {
        return Math.round((interval / 24 / 3600)) + "天前";
    } else if (interval > 3600) {
        return Math.round((interval / 3600)) + "小时前";
    } else if (interval > 60) {
        return Math.round((interval / 60)) + "分钟前";
    } else {
        return "刚刚";
    }
}


$(function () {
    $('.modifyButton').click(function () {
        $('.tableContainer').empty();
        $('.tableTitle').text('变更的配置');
        getConfig(1);
    });
    $('.allButton').click(function () {
        $('.tableContainer').empty();
        $('.tableTitle').text('全部配置');
        getConfig(2);
    });
});

function getConfig(configType) {

    $.ajax({
        url: "/api/release-history/get-release-history-config",
        type: "get",
        data:{releaseName:$('.version').text(),configType:configType},
        success: function (data) {
            if (data.code==0){
                var  str='';
                if(configType==1){
                    str=generateModifyConfigForModifyConfig(data.data);
                }else if(configType==2){
                    str=generateModifyConfigForAllConfig(data.data);
                }
                $('.tableContainer').append(str);
            }else {
                alert("获取项目信息失败 code 不等于0"+data.message);
            }
        },
        error: function (err) {
            alert("直接error了"+err['responseJSON']['message']);
        }
    });
}

function generateModifyConfigForModifyConfig(data) {
    var modifyType={1:"新增",2:"修改",3:"删除"};

    var str='<table class="table table-striped table-bordered table-hover"><tr><td>Type</td><td>Key</td><td>Old Value</td><td>New Value</td></tr>';
    for (var k in data){
        str+='<tr><td>'+modifyType[data[k]["modify_type"]]+'</td><td>'+data[k]["key"]+'</td><td>'+data[k]["old_value"]+'</td><td>'+data[k]["new_value"]+'</td></tr>';
    }
    str+='</table>';
    return str;
}

function generateModifyConfigForAllConfig(data) {
    var str='<table class="table table-striped table-bordered table-hover"><tr><td>Key</td><td>Value</td></tr>';
    for (var k in data){
        str+='<tr><td>'+data[k]["key"]+'</td><td>'+data[k]["value"]+'</td></tr>';
    }
    str+='</table>';
    return str;
}