<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\SysConfig;


class Sys_setupController extends BasicController
{
    /**
     * 系统设置
     */
    public function actionIndex()
    {
    	/*如果有数据，进行修改*/
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $sysConfigModel = new SysConfig;
            if($sysConfigModel->set($post)){
                return ShowRes(0, '修改成功');
                Yii::$app->end();
            }else{
                if($sysConfigModel->hasErrors()){
                    return ShowRes(30010, $sysConfigModel->getErrors());
                }else{
                    return ShowRes(30000, '修改失败');
                }
            }
            return;
        }

        $sysConfig = SysConfig::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->one();
        // P($sysConfig);
    	return $this->render('index', [
            'sysConfig' => $sysConfig
        ]);

    }


}
