$(function () {
    $.ajax({
        url: "/api/layout/get-project-info",
        type: "post",
        success: function (data) {
            if (data.code==0){
                render(data['data']);
            }else {
                alert("获取项目信息失败 code 不等于0"+data.message);
            }
        },
        error: function (err) {
            alert("直接error了"+err['responseJSON']['message']);
        }
    });
});
function render(data) {
    $('#callback').bigAutocomplete({
        data:data,
        title:'text',
        callback:function(row){
          window.location='/common-config-data/index?app_id='+row.value;
        },
    });
}