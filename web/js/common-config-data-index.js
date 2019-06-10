$(function() {
    $('#modify-history-button').click(function() {
        $('.project-config-info-display').css('display','none');
        $('.modify-history-container').empty();
        $.ajax({
            url: "/api/common-config-data/get-config-data-modify-log",
            type: "get",
            success: function (data) {
                if (data.code==0){
                    var  b=generateTable(data.data);
                    $('.modify-history-container').append(b);
                }else {
                    alert("获取项目信息失败 code 不等于0"+data.message);
                }
            },
            error: function (err) {
                alert("直接error了"+err['responseJSON']['message']);
            }
        });
    });

    $('#table-button').click(function () {
        $('.modify-history-container').empty();
        $('.project-config-info-display').css('display','');

    });
});


function generateTable(data) {
    var str ='';
    for (var i=0;i<data.length;i++){
        str+='<div><div><div style="font-size:22px"><div style="display: inline">'+data[i].create_name+'</div><div style="display: inline-block;float: right">'+data[i].create_time+'</div></div></div><div class="table-responsive"><table class="table table-bordered"><tr><td>Type</td><td>Key</td><td>Old Value</td><td>New Value</td><td>Comment</td></tr><tr><td>'+data[i].modify_type+'</td><td>'+data[i].key+'</td><td>'+data[i].old_value+'</td><td>'+data[i].new_value+'</td><td>'+data[i].comment+'</td></tr></table></div></div>';
    }
    return str;
}

function generateTableForPublishPublic(willFullStr) {
    return '<table class="table table-bordered table-hover" ><thead><tr><th>Key</th><th>发布的值</th><th>未发布的值</th><th>修改人</th><th>修改时间</th></tr></thead><tbody>'+willFullStr+'</tbody></table>';
}

function getType(type) {
    if(type==1){
        return '<span class="btn-success">增</span>';
    }else if(type==2){
        return '<span class="btn-info">改</span>';
    }else if(type==3){
        return '<span class="btn-warning">删</span>';
    }
}

function generateTableForPublish(data) {
    var tmpStr='';
    if(data.length<=0){
        return '<div>无配置数据</div>';
    }else{
        for (let key in data){
            tmpStr+='<tr><td>'+key+getType(data[key]['status'])+'</td><td>'+data[key]['alreadyRelease']+'</td><td>'+data[key]['notRelease']+'</td><td>'+data[key]['modifyName']+'</td><td>'+data[key]['updateTime']+'</td></tr>';
        }
        tmpStr=generateTableForPublishPublic(tmpStr);
        return tmpStr;
    }
    throw '未知错误，请检查';
}

$(function () {
  $('.index-release').click(function () {
      $.ajax({
          url: "/api/release/get-release-changes",
          type: "get",
          success: function (data) {
              if (data.code==0){
                  var str=generateTableForPublish(data.data);
                  $('.change>table').remove();
                  $('.change>div').remove();
                  $('.change').append(str);
              }else {
                  alert("获取项目信息失败 code 不等于0"+data.message);
              }
          },
          error: function (err) {
              alert("直接error了"+err['responseJSON']['message']);
          }
      });
  });
});



$(function () {
    $('.index-toggle-release').click(function () {
        $.ajax({
            url: "/api/release/release",
            type: "post",
            data:{releaseName:$('.release-release-name').val(),releaseComment:$('.release-release-comment').val()},
            success: function (data) {
                if (data.code==0){
                    window.location.href='/common-config-data/index';
                }else {
                    alert("获取项目信息失败 code 不等于0"+data.message);
                }
            },
            error: function (err) {
                alert("直接error了"+err['responseJSON']['message']);
            }
        });
    });
});