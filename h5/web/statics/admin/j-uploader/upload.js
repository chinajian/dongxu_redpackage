/*
上传弹窗
url                 服务器URL
count               数目限制
fn                  回调函数
*/
function openUploadLayer(args)
{
    var url = args.url?args.url:'',
        count = args.count?args.count:1,
        defaultImg = args.defaultImg?args.defaultImg:'',
        fn = args.fn;

    if(defaultImg !== ''){
        successCount = 1;
    }else{
        successCount = 0;
    }

    if(!url){
        console.log('parameter is error');
        return;
    }
    if(count > 10){
        console.log('最多上传10张');
        return;   
    }
    var index = layer.open({
        type: 1,
        title: '插入图片',
        area: ['800px', '530px'],
        shadeClose: false, //点击遮罩关闭
        content: `<div class="bs-example bs-example-tabs j-upload" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class=""><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">相册选择</a></li>
                            <li role="presentation" class="active"><a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="false">本地上传</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <!--系统相册-->
                            <div class="form-horizontal">
                                <label class="col-sm-2 control-label">选择相册</label>
                                <div class="col-sm-9">
                                    <select class="form-control input-sm" id="albumCategory">
                                        <option value="1">我的相册</option>
                                    </select>
                                </div>
                            </div>
                            <!--相册选择-->
                            <div role="tabpanel" class="tab-pane fade" id="home" aria-labelledby="home-tab">
                                
                            </div>
                            <!--本地上传-->
                            <div role="tabpanel" class="tab-pane fade active in" id="profile" aria-labelledby="profile-tab">
                                <div class="form-horizontal">
                                    <label class="col-sm-2 control-label">上传图片</label>
                                    <div class="col-sm-9">
                                        <div id="image-picker" class="btn btn-primary btn-block">选择图片并上传...</div>
                                    </div>
                                </div>
                            </div>
                            <!--上传成功后提示-->
                            <div class="alert alert-success" style="display:none" role="alert">
                                <span class="glyphicon glyphicon-ok"></span> 上传成功，共<span class="success">0</span>张！
                            </div>
                            <!--上传失败后提示-->
                            <div class="alert alert-danger" style="display:none" role="alert">
                                <span class="glyphicon glyphicon-ok"></span> 上传失败，共<span class="fail">0</span>张！
                            </div>
                            <!--上传的图片列表-->
                            <div class="album-list">
                                <h3>要插入的图片(<span class="success-count">` + successCount + `</span>/` + count + `)</h3>
                                <div class="list">
                                    <!--已经上传的图片列表-->
                                    <ul class="imgs"></ul>
                                    <!--图片列表位置-->
                                    <ul class="bg">
                                    </ul>
                                </div>
                            </div>
                            <div class="submit"><button type="button" class="btn btn-primary btn-block">插入图片</button></div>
                            <!--上传进度-->
                            <div class="progress-list">
                            </div>
                        </div>
                    </div>`,
        success: function(){
            /*根据上传数量，创建空位*/
            for(var i=0; i<count; i++){
                $(".bg").append('<li><div class="glyphicon glyphicon-picture"></div></li>');
            }

            if(defaultImg === ''){//如果没有默认图片
                /*创建webuploader*/
                create(url, '.j-upload #image-picker', '.imgs', '.j-upload .progress-list', count);
            }else{//有默认图片
                $('.imgs').append('<li id="default-img"><img src="' + defaultImg + '"><span class="del-img"></span></li>');
                /*删除默认图片 事件*/
                $("#default-img span").click(function(){
                    $(this).parent().remove();//删除默认图片
                    $('.success-count').html(0);//已经上传的数量改成0
                    /*创建webuploader*/
                    create(url, '.j-upload #image-picker', '.imgs', '.j-upload .progress-list', count);
                })
            }

            /*绑定 插入图片 按钮 触发回调函数*/
            $('.submit').click(function(){
                if((typeof fn) === 'function'){
                    fn($('.imgs>li'));//执行回调函数
                }
                /*关闭整个弹窗*/
                layer.close(index);
            })
        }
    });
}


/*
创建上传插件
uploadObj               需要创建的对象
url                     服务器接受url
showlistObj             显示列表
progresslistObj         进程显示列表
*/
function create(url, uploadObj, showlistObj, progresslistObj, count)
{
    var url = url,
        success = 0,                //成功上传了多少张
        fail = 0,                   //上传失败了多少张
        maxSize = 2,                //不要超过2M
        count = count?count:1;      //最多上传几张图片

    var $list = $(showlistObj),
        $progressList = $(progresslistObj),

        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1,

        // 缩略图大小
        thumbnailWidth = 70 * ratio,
        thumbnailHeight = 70 * ratio,

        // Web Uploader实例
        uploader;

    uploader = WebUploader.create({
        auto: true,// 选完文件后，是否自动上传。
        // swf: '<?php echo COMMON_SITE_URL;?>webuploader/Uploader.swf',// swf文件路径
        server: url,// 文件接收服务端。
        // 选择文件的按钮。可选。//内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: uploadObj,
        //上传数量
        fileNumLimit: count,
        duplicate: false,
        // formData: {'aaa': '222', 'bbb': '3333'}, //文件上传请求的参数表，每次发送都会发送此对象中的参数。
        fileVal: 'UploadForm[file]',//设置文件上传域的name[默认值：'file']
        // addFiles: [{"id":"WU_FILE_0", "name":"1478598670983.jpg"}],
        fileSingleSizeLimit: maxSize*1024*1024,   //设定单个文件大小
        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png'
        },
        thumb: {
            // 是否允许裁剪。
            crop: false,
            // 为空的话则保留原有图片格式。
            // 否则强制转换成指定的类型。
            type: 'image/jpg,image/jpeg,image/png'
        }
    });

    //当某个文件的分块在发送前触发，主要用来询问是否要添加附带参数，大文件在开起分片上传的前提下此事件可能会触发多次。
    uploader.on('uploadBeforeSend', function (obj, data, headers) {
        data.catId = $('#albumCategory').val();//带上相册ID
    });
    // 当有文件添加进来的时候
    uploader.on('fileQueued', function(file) {
        /*放入进度条*/
        var $li = $(
                '<div class="progress" id="' + file.id + '_PROGRESS">' +
                    '<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
                    '</div>' +
                '</div>'
                );

        $progressList.append($li);

    });
    // 文件上传过程中创建进度条实时显示。
    uploader.on('uploadProgress', function(file, percentage) {
        var $li = $( '#'+file.id+'_PROGRESS'),
            $percent = $li.find('div');

        $percent.css('width', percentage * 100 + '%');
    });
    // 文件上传成功。
    uploader.on('uploadSuccess', function(file, response) {
        if(response.code == 0){
            var $li = $(
                '<li id="' + file.id + '"><img src="' + response.mes + '"><span class="del-img"></span></li>'
                ),
            $img = $li.find('img');
            $list.append($li);

            success++;
            /*更新已经成功上传的数量*/
            $(".j-upload .success-count").html(parseInt($(".j-upload .success-count").html())+1);
        }else{//如果后台反应失败
            uploader.removeFile(file);//队列中删除
        }
        
    });
    // 文件上传失败。
    uploader.on('uploadError', function(file, response) {
        fail++;
        uploader.removeFile(file);//队列中删除
    });
    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on('uploadComplete', function(file) {
        $('#'+file.id+'_PROGRESS').fadeOut(function(){
            $(this).remove();
        });
    });
    //删除事件
    $list.on("click", "li>span", function () {
        self = $(this).parent();
        var id = $(self).attr("id");
        uploader.removeFile(id, true);
        $(self).fadeOut(function () {
            $(self).remove();
            /*更新已经成功上传的数量*/
            $(".j-upload .success-count").html(parseInt($(".j-upload .success-count").html())-1);
        });
    });
    //ERROR
    uploader.on('error', function(handler) {
        if (handler == 'Q_EXCEED_NUM_LIMIT') {
            layer.msg('最多传' + count + '张照片');
        } else if (handler == 'F_DUPLICATE') {
            layer.msg('文件已存在队列中');
        }else if (handler == 'Q_TYPE_DENIED') {
            layer.msg('文件类型不满足');
        }else if (handler == 'F_EXCEED_SIZE') {
            layer.msg('文件大小不能超过' + maxSize + 'M');
        }
    });
    // 当所有文件上传结束时触发。
    uploader.on('uploadFinished', function(file) {
        /*更改上传成功的数量(在弹出来的提示中修改)*/
        $('.j-upload .success').html(success);
        success = 0;//本次上传成功的数量清零

        /*显示 上传了多少张，成功了多少张*/
        $('.j-upload .success').parent().fadeIn();
        var t = setTimeout(function(){
            $('.j-upload .success').parent().fadeOut();
        }, 3000);


        if(fail){//如果有失败
            /*更改上传失败的数量(在弹出来的提示中修改)*/
            $('.j-upload .fail').html(fail);
            fail = 0;//本次上传失败的数量清零

            /*显示 上传了多少张，失败了多少张*/
            $('.j-upload .fail').parent().fadeIn();
            var t = setTimeout(function(){
                $('.j-upload .fail').parent().fadeOut();
            }, 3000);
        }

    });
};