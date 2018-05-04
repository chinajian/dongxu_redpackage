<?php 
    use yii\helpers\Url;
?>
<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>datetimepicker/bootstrap-datetimepicker.min.css"/>

<nav class="navbar navbar-default child-nav">
    <h5 class="nav pull-left">系统设置</h5>
</nav>
<form class="form-horizontal" id="sysForm">
    <div class="form-group">
        <label for="activity_name" class="col-sm-2 control-label">活动名称</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="SysConfig[activity_name]" id="activity_name" placeholder="活动名称" value="<?php echo $sysConfig['activity_name']?>">
        </div>
    </div>
    <div class="form-group">
        <label for="exchange_code" class="col-sm-2 control-label">兑换码</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="SysConfig[exchange_code]" id="exchange_code" placeholder="兑换码" value="<?php echo $sysConfig['exchange_code']?>">
            <span class="help-block">如果有线下奖品核销，请填写核销码</span>
        </div>
    </div>
    <div class="form-group">
        <label for="begin_time" class="col-sm-2 control-label">活动开始时间</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm form_datetime" name="SysConfig[begin_time]" id="begin_time" placeholder="活动开始时间" value="<?php echo $sysConfig['begin_time']?date('Y-m-d H:i', $sysConfig['begin_time']):''?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label for="end_time" class="col-sm-2 control-label">活动结束时间</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm form_datetime" name="SysConfig[end_time]" id="end_time" placeholder="活动结束时间" value="<?php echo $sysConfig['end_time']?date('Y-m-d H:i', $sysConfig['end_time']):''?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label for="is_close" class="col-sm-2 control-label">是否关闭</label>
        <div class="col-sm-9 iCheck">
            <label class="radio-inline">
                <input type="radio" name="SysConfig[is_close]" value="0" <?php if($sysConfig['is_close'] === '0'){?>checked<?php }?>> 否
            </label>
            <label class="radio-inline">
                <input type="radio" name="SysConfig[is_close]" value="1" <?php if($sysConfig['is_close'] === '1'){?>checked<?php }?>> 是
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="is_test" class="col-sm-2 control-label">启动测试</label>
        <div class="col-sm-9 iCheck">
            <label class="radio-inline">
                <input type="radio" name="SysConfig[is_test]" value="0" <?php if($sysConfig['is_test'] === '0'){?>checked<?php }?>> 关闭
            </label>
            <label class="radio-inline">
                <input type="radio" name="SysConfig[is_test]" value="1" <?php if($sysConfig['is_test'] === '1'){?>checked<?php }?>> 启动
            </label>
        </div>
    </div>
<!--     <div class="form-group">
        <label for="end_time" class="col-sm-2 control-label">APPID</label>
        <div class="col-sm-9"><span style="padding-top: 5px; display: inline-block;"><?php echo $sysConfig['appid']?></span></div>
    </div> -->
    <div class="form-group">
        <div class="col-sm-10 col-md-offset-2">
            <button type="button" class="btn btn-primary btn-sm" id="mod"><span class="glyphicon glyphicon-ok"></span> 提交</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    /*创建日期*/
    $('.form_datetime').datetimepicker({
        language:  'zh-CN',
        format: "yyyy-mm-dd hh:ii",
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        minView: 0,
        maxView: 1
    });

    /*修改*/
    var successUrl = '<?php echo Url::to(['sys_setup/index'])?>';
    $("#mod").click(function(){
        jajax("<?php echo Url::to(['sys_setup/index'])?>", $('#sysForm').serialize());
    })
</script>