<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
    <h5 class="nav pull-left">修改管理员</h5>
    <div class="nav pull-right">
        <a href="<?php echo yii\helpers\Url::to(['manager/manager-list'])?>" type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-th-list"></span> 管理员列表</a>
    </div>
</nav>
<form class="form-horizontal" id="modForm">
    <input type="hidden" class="form-control input-sm" name="Admin[user_id]" id="user_id" placeholder="用户名" value="<?php echo $manager['user_id']?>">
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">用户名<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" disabled="true" name="Admin[username]" id="username" placeholder="用户名" value="<?php echo $manager['username']?>">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">用户密码</label>
        <div class="col-sm-9">
            <input type="password" class="form-control input-sm" name="Admin[password]" id="password" placeholder="用户密码">
        </div>
    </div>
    <div class="form-group">
        <label for="passwordConfirm" class="col-sm-2 control-label">重复密码</label>
        <div class="col-sm-9">
            <input type="password" class="form-control input-sm" name="Admin[passwordConfirm]" id="passwordConfirm" placeholder="重复密码">
        </div>
    </div>
    <div class="form-group">
        <label for="state" class="col-sm-2 control-label">状态</label>
        <div class="col-sm-9 iCheck">
            <label class="radio-inline">
                <input type="radio" name="Admin[state]" id="state1" value="1" <?php if($manager['state'] == 1){?>checked<?php }?>> 启动
            </label>
            <label class="radio-inline">
                <input type="radio" name="Admin[state]" id="state2" value="2" <?php if($manager['state'] == 2){?>checked<?php }?>> 禁用
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="realname" class="col-sm-2 control-label">用户姓名<span class="mandatory">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="Admin[realname]" id="realname" placeholder="用户姓名" value="<?php echo $manager['realname']?>">
        </div>
    </div>
    <div class="form-group">
        <label for="phone" class="col-sm-2 control-label">手机号码</label>
        <div class="col-sm-9">
            <input type="text" class="form-control input-sm" name="Admin[phone]" id="phone" placeholder="手机号码" value="<?php echo $manager['phone']?>">
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">邮箱</label>
        <div class="col-sm-9">
            <input type="email" class="form-control input-sm" name="Admin[email]" id="email" placeholder="邮箱" value="<?php echo $manager['email']?>">
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
        jajax("<?php echo Url::to(['manager/mod-manager'])?>", $('#modForm').serialize());
    })
</script>