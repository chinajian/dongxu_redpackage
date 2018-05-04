<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.css"/>

<link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>webuploader/webuploader.css"/>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>j-uploader/upload.css"/>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>css/upload.css"/>
<script src="<?php echo COMMON_SITE_URL;?>webuploader/webuploader.min.js"></script>
<script src="<?php echo ADMIN_SITE_URL;?>j-uploader/upload.js"></script>
<style type="text/css">
    .choosePrize{
        width: 20%;
        display: inline-block;
        padding-top: 5px;
    }
    .choosePrize input{
        margin-right: 10px;
        vertical-align: top;
    }
</style>
<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
    <h5 class="nav pull-left">修改配比</h5>
    <div class="nav pull-right">
        <a href="<?php echo yii\helpers\Url::to(['ratio/ratio-list'])?>" type="button" class="btn btn-info btn-xs" style='margin-top: 9px;'><span class="glyphicon glyphicon-th-list"></span> 配比列表</a>
    </div>
</nav>
<form class="form-horizontal" id="modForm">
    <input type="hidden" class="form-control input-sm" name="Ratio[id]" id="id" value="<?php echo $ratio['id']?>">
    <div class="form-group">
        <label for="sid" class="col-sm-2 control-label">请选择场次<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <select class="form-control" name="Ratio[sid]" id="sid">
                <option value="">请选择场次</option>
                <?php foreach($season as $k => $v){?>
                    <option value="<?php echo $v['sid'];?>" <?php if($ratio['sid'] == $v['sid']){?>selected<?php }?>><?php echo $v['season_name'];?>（<?php echo date('Y-m-d H:i:s', $v['luckydraw_begin_time']);?> ~ <?php echo date('Y-m-d H:i:s', $v['luckydraw_end_time']);?>）</option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="total_num" class="col-sm-2 control-label">奖品总数量<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="Ratio[total_num]" id="total_num" placeholder="奖品总数量" value="<?php echo $ratio['total_num']?>">
        </div>
    </div>
    <div class="form-group">
        <label for="pack_name" class="col-sm-2 control-label">选择奖品<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <?php foreach($prize as $k => $v){?>
                <div class="choosePrize">
                    <label>
                        <input type="radio" name="Ratio[pid]" value="<?php echo $v['pid'];?>" <?php if($ratio['pid'] == $v['pid']){?>checked<?php }?>><?php echo $v['prize_name'];?>
                    </label>
                </div>
            <?php }?>
        </div>
    </div>
    <div class="form-group" style="display: none">
        <label for="red_packet_money" class="col-sm-2 control-label">设置概率</label>
        <div class="col-sm-9">
            
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-md-offset-2">
            <button type="reset" class="btn btn-default btn-sm" data-dismiss="modal"><span class="glyphicon glyphicon-repeat"></span> 重置</button>
            <button type="button" class="btn btn-primary btn-sm" id="mod"><span class="glyphicon glyphicon-ok"></span> 提交</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    /*修改*/
    $("#mod").click(function(){
        jajax("<?php echo Url::to(['ratio/mod-ratio'])?>", $('#modForm').serialize());
    })

   
</script>