<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;


class PublicController extends Controller
{
	/**
     * 登录页面
     */
    public function actionLogin()
    {
    	if(Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		$admin = new Admin;
    		if($admin->login($post)){
                return showRes(200, '登录中，请稍后...', Url::to(['sys_setup/index']));
                Yii::$app->end();
            }else{
                if($admin->hasErrors()){
                    return showRes(300, $admin->getErrors());
                }
            }
    		return;
    	}
    	$this->layout = false;
        return $this->render('login');
    }

    /*
    登出
    */
    public function actionLogout()
    {
        AdminInfo::clearLoginInfo();
        if(!AdminInfo::getIsLogin()){
            return showRes(200, '登出成功', Url::to(['public/login']));
            Yii::$app->end();
        };
        return showRes(300, '登出失败');
    }

    /*百度编辑器验证*/
    public function actionUeditor()
    {
        return '{}';
    }

}
