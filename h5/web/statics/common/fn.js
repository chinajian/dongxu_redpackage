/*
ajax请求
url     请求的网址
data    提交的数据(POST方式) json对象
fn      回调函数
type    返回方式(text,json,xml)
method  请求方式(POST GET)
*/
function jajax(url, data, fn, type, method)
{
    var url = arguments[0]?arguments[0]:"";
    var data = arguments[1]?arguments[1]:"";
    var fn = arguments[2]?arguments[2]:resFn;
    var type = arguments[3]?arguments[3]:"text";
    var method = arguments[4]?arguments[4]:"post";
    if(url == ""){
        return false;
    }
    $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: type,
        cache: false,
        success: function (response) {
            fn(response);
        },
        error: function (e) {
            showError('error');
        }
    });
}

/*
ajax提交表单后，通用处理函数
res     后来返回的json数据
*/
var resFn = function(res)
{
    if(res){
        if(isJSON(res)){//是JSON格式
            res = JSON.parse(res);
            if(res.code == 0){
                showSuccess(res.mes);
                if(res.url != ""){
                    setTimeout(function(){
                        if(res.url == 'refresh'){
                            window.location.reload();
                        }else if(res.url == 'back'){
                            window.history.back();
                        }else{
                            window.location.href = res.url;
                        }
                    }, 500);
                    return;
                }
                if((typeof successUrl != 'undefined') && successUrl){
                    setTimeout(function(){
                        if(successUrl == 'refresh'){
                            window.location.reload();
                        }else if(successUrl == 'back'){
                            window.history.back();
                        }else{
                            window.location.href = successUrl;
                        }
                    }, 500);
                    return;
                }
            }else{
                if(typeof(res.mes)  == 'object'){
                    $.each(res.mes, function(k, v) {
                        if(typeof(v[0])  == 'object'){
                            $.each(v[0], function(k2, v2) {
                                showError(v2);
                            });
                        }else{
                            showError(v);
                        }
                    });
                }else{
                    showError(res.mes);
                }
            }
        }
    }else{//空数据
        showError('数据有误！！！');
    }
}

/*
回调函数的成功，信息提示
info 提示的信息
*/
function showSuccess(info)
{
    iziToast.success({
        title: 'OK',
        timeout: 5000,//5秒钟后消失
        message: info,
        position: 'bottomRight',//右下角显示
        transitionIn: 'flipInX',
        icon: 'glyphicon glyphicon-ok',
    });
};


/*
回调函数的失败，信息提示
info 提示的信息
*/
function showError(info)
{
    iziToast.error({
        title: 'Error',
        message: info,
        position: 'bottomRight',
        transitionIn: 'fadeInDown',
        icon: 'glyphicon glyphicon-remove',
    });
}

/*
判断字符串是否为json格式
*/
function isJSON(str) {
    if (typeof str == 'string') {
        try {
            var obj=JSON.parse(str);
            if(str.indexOf('{')>-1){
                return true;
            }else{
                return false;
            }

        } catch(e) {
            return false;
        }
    }
    return false;
}


/*
触发提醒
obj         jq对象
okFn        确定执行函数
cancelFn    取消执行函数
*/
function confirmation(obj, okFn, cancelFn)
{
    $('.del').confirmation({
     'title': '確定刪除吗?',
     'btnOkClass':  'btn-primary btn-xs',
     'btnCancelClass':  'btn-default btn-xs',
     'btnOkLabel': '<i class="glyphicon glyphicon-ok-sign"></i> 確定',
     'btnCancelLabel': '<i class="glyphicon glyphicon-remove-sign"></i> 取消',
     'onConfirm': function(){
        if(typeof(okFn) != 'function'){
            console.log("cancel");
        }else{
            okFn();
        }
     },
     'onCancel': function(){
        if(typeof(cancelFn) != 'function'){
            console.log("cancel");
        }else{
            cancelFn();
        }
     },
    });    
}



/*
创建分页
numCount    总条数
currPage    当前页面
url         点击按钮触发 获取新内容 的链接
fn          获取新数据后的回调函数
*/
function createPage(numCount, currPage, url, fn){
    pageNum = 20;// 每页显示数目
    pageCount = Math.ceil(numCount/pageNum);//总条数 除以 每页条数  得到 一共多少页，向上取整

    if(isCreatePage){
        isCreatePage = !isCreatePage;//只能创建一次，否则将出现多个分页闭包
        $(".pagelist").createPage({
            pageCount: pageCount,//总页数
            current: currPage,//当前页
            backFn:function(p){
                window.currPage = p;
                jajax(DIR + url + "/page/" + p, "", fn);
            }
        });
    }
}