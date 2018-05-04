<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.css"/>

<link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>webuploader/webuploader.css"/>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>j-uploader/upload.css"/>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>css/upload.css"/>
<script src="<?php echo COMMON_SITE_URL;?>webuploader/webuploader.min.js"></script>
<script src="<?php echo ADMIN_SITE_URL;?>j-uploader/upload.js"></script>
<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
    <h5 class="nav pull-left">添加奖品</h5>
    <div class="nav pull-right">
        <a href="<?php echo yii\helpers\Url::to(['prize/prize-list'])?>" type="button" class="btn btn-info btn-xs" style='margin-top: 9px;'><span class="glyphicon glyphicon-th-list"></span> 奖品列表</a>
    </div>
</nav>
<form class="form-horizontal" id="addForm">
    <div class="form-group">
        <label for="prize_name" class="col-sm-2 control-label">奖品名称<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="Prize[prize_name]" id="prize_name" placeholder="奖品名称">
        </div>
    </div>
    <div class="form-group">
        <label for="prize_img" class="col-sm-2 control-label">奖品图片</label>
        <div class="col-sm-9">
            <ul id="upload">
                <li class="empty"></li>
                <li class="empty"></li>
                <li class="empty"></li>
            </ul>
        </div>
    </div>
    <div class="form-group">
        <label for="is_red_packet" class="col-sm-2 control-label">微信红包</label>
        <div class="col-sm-9 iCheck">
            <label class="radio-inline">
                <input type="radio" name="Prize[is_red_packet]" value="0" checked> 否
            </label>
            <label class="radio-inline">
                <input type="radio" name="Prize[is_red_packet]" value="1"> 是
            </label>
        </div>
    </div>
    <div class="form-group" style="display: none">
        <label for="red_packet_money" class="col-sm-2 control-label">红包金额</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="Prize[red_packet_money]" id="red_packet_money" placeholder="红包金额">
            <span class="help-block">红包金额以“分”为单位</span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-md-offset-2">
            <button type="reset" class="btn btn-default btn-sm" data-dismiss="modal"><span class="glyphicon glyphicon-repeat"></span> 重置</button>
            <button type="button" class="btn btn-primary btn-sm" id="add"><span class="glyphicon glyphicon-ok"></span> 提交</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    /*选择红包*/
    $('input[name="Prize[is_red_packet]"]').click(function(){
        var is_red_packet = $(this).val();
        if(is_red_packet == 1){
            $("#red_packet_money").parent().parent().show();
        }else{
            $("#red_packet_money").parent().parent().hide();
            $("#red_packet_money").val('');
        }
    })

    /*添加*/
    $("#add").click(function(){
        /*奖品图片，循环出一个input列表>>>*/
            $('#upload').nextAll().remove();
            $('#upload').find('.full').each(function(){
                $('#upload').parent().append(`<input type="hidden" name="Prize[prize_img][]" value="`+ $(this).find('img').attr('src') +`">`);
            })
        /*奖品图片，循环出一个input列表<<<*/
        jajax("<?php echo Url::to(['prize/add-prize'])?>", $('#addForm').serialize());
    })

    /*图片 上传*/
    $("#upload li").click(function(event){
        /*删除图片*/
        if(event.originalEvent.target.tagName.toLowerCase() === 'span'){//点的是span关闭按钮
            $(this).children().fadeOut(function () {
                $(this).parent().attr('class', 'empty');
                $(this).remove();
                $("#prize_img").val('');
            });
            return;
        }

        if($(this).attr('class') === 'full'){
            var self = this;
            var args = {
                'url': '<?php echo Url::to(['basic/upload-file'])?>',
                'count': 1,
                'defaultImg': $(this).find('img').attr('src'),
                'fn': function(res){
                    var imgUrl = res.eq(0).find('img').attr('src');
                    if(imgUrl){
                        $(self).attr('class', 'full');
                        $(self).html('<img src="' + imgUrl + '"><span class="del-img">')
                    }
                    $("#prize_img").val(imgUrl);
                }
            }
        }else if($(this).attr('class') === 'empty'){
            var args = {
                'url': '<?php echo Url::to(['basic/upload-file'])?>',
                'count': $("#upload li.empty").length,
                'fn': function(res){
                    $('#upload li.empty').each(function(index, element){
                        var imgUrl = res.eq(index).find('img').attr('src');
                        if(imgUrl){
                            $(this).attr('class', 'full');
                            $(this).html('<img src="' + imgUrl + '"><span class="del-img">')
                        }
                    });

                    var shopLogoArr = new Array();
                    $('#upload li.full').each(function(index, element){
                        shopLogoArr.push($(this).find('img').attr('src'));
                    });
                    shopLogo = shopLogoArr.join(',');
                    $("#prize_img").val(shopLogo);

                }
            }
        }
        openUploadLayer(args);
    });
</script>