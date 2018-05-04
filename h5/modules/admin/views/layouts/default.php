<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>抽奖管理系统</title>
    <link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>izitoast/css/iziToast.min.css"/>
    <link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>paging/paging.css"/>
    <link rel="stylesheet" href="<?php echo COMMON_SITE_URL;?>common.css"/>
    <link rel="stylesheet" href="<?php echo ADMIN_SITE_URL;?>css/default.css"/>
    <script src="<?php echo COMMON_SITE_URL;?>jquery.min.js"></script>
    <script src="<?php echo COMMON_SITE_URL;?>bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo COMMON_SITE_URL;?>bootstrap/bootstrap-tooltip.js"></script>
    <script src="<?php echo COMMON_SITE_URL;?>bootstrap/bootstrap-confirmation.js"></script>
    <script src="<?php echo ADMIN_SITE_URL;?>izitoast/js/iziToast.min.js"></script>
    <script src="<?php echo ADMIN_SITE_URL;?>layer-v3.1.0/layer.js"></script>
    <script src="<?php echo ADMIN_SITE_URL;?>paging/paging.js"></script>
    <script src="<?php echo COMMON_SITE_URL;?>fn.js"></script>
</head>
<body>
    <header class="sysinfo noselect">
        <nav class="navbar navbar-default navbar-inverse" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navlist">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">抽奖管理系统</a>
                </div>

                <div class="collapse navbar-collapse" id="navlist">
                    <ul class="nav navbar-nav">
                        <li><a href="#">管理控制台</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><?php echo Yii::$app->session['admin']['username'];?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:void(0);" id="logout">退出</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="viewFramework-body">
        <!-- 一级菜单 -->
        <section class="viewFramework-sidebar noselect">
        	<!-- 隐藏主菜单按钮 -->
            <div class="sidebar-inner">
                <span class="glyphicon glyphicon-align-justify"></span>
            </div>
            <ul>
                <?php foreach($this->params['menu'] as $k => $v){?>
                    <li data-toggle="tooltip" data-placement="right"><span class="glyphicon glyphicon-<?php echo $v["icon"]?>"></span><span><?php echo $v["name"]?></span></li>
                <?php }?>
            </ul>
        </section>
        <div class="viewFramework-navbar-con">
            <!-- 二级菜单 -->
            <section class="viewFramework-navbar noselect">
                <?php foreach($this->params['menu'] as $k => $v){?>
                    <div class="hide">
                        <h2><?php echo $v["name"]?></h2>
                        <ul>
                            <?php foreach($v["children"] as $k2 => $v2){?>
                                <li class="<?php if(Yii::$app->controller->id == $v2['c']){?>active<?php }?>">
                                    <a href="<?php echo yii\helpers\Url::to([$v2['c'].'/'.$v2['a']])?>"><?php echo $v2["name"]?></a>
                                </li>
                            <?php }?>
                        </ul>
                    </div>
                <?php }?>
            </section>
            <!-- 页面主内容 -->
            <section class="viewFramework-content"><?= $content ?></section>
        </div>
    </div>
    <script type="text/javascript">
        /*初始化 显示标题*/
        var index = $('.viewFramework-navbar ul li.active').parent().parent().index();
        $('.viewFramework-sidebar ul li').eq(index).attr('class', 'active');
        $('.viewFramework-navbar>div').eq(index).attr('class', 'active');
        
        /*点击主导航,切换二级菜单*/
        $(".viewFramework-sidebar ul li").click(function(){
            $(this).addClass("active").siblings().removeClass("active");
            var index = $(this).index();
            $(".viewFramework-navbar>div").eq(index).removeClass("hide").siblings().addClass("hide");
        })
        /*登出*/
        $("#logout").click(function(){
            jajax("<?php echo yii\helpers\Url::to(['public/logout'])?>");
        })
    </script>
</body>
</html>