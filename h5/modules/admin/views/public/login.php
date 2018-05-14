<?php 
	use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<title>后台管理</title>
	<link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>bootstrap/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>izitoast/css/iziToast.min.css"/>
	<link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>common.css"/>
	<link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>css/login.css"/>
</head>
<body>
	<div class="login">
		<div class="login-content">
			<div class="col-xs-3 col-xs-offset-3 logo">
				<img src="<?php echo ADMIN_SITE_URL;?>images/logo.png">
			</div>
			<div class="col-xs-3 col-xs-offset-7 login-form">
				<form class="form-horizontal" id="loginForm">
					<!-- <input type="hidden" id="_csrf" name="<?php echo Yii::$app->request->csrfParam;?>" value="<?php echo yii::$app->request->csrfToken?>"> -->
					<div class="form-group">
						<label for="username" class="col-sm-4 control-label">用户名</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="username" name="Admin[username]" placeholder="用户名">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-4 control-label">密码</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" id="password" name="Admin[password]" placeholder="密码">
						</div>
					</div>
					<button type="button" class="btn btn-info btn-block" id="login">登录系统</button>
				</form>
			</div>
		</div>
	</div>
	<script src="<?php echo COMMON_SITE_URL;?>jquery.min.js"></script>
	<script src="<?php echo COMMON_SITE_URL;?>bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo ADMIN_SITE_URL;?>izitoast/js/iziToast.min.js"></script>
	<script src="<?php echo COMMON_SITE_URL;?>fn.js"></script>
	<script type="text/javascript">
		$("#login").click(function(){
			jajax("<?php echo Url::to(['public/login'])?>", $('#loginForm').serialize());
		})
	</script>
</body>
</html>