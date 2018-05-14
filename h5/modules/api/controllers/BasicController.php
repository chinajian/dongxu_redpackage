<?php

namespace app\modules\api\controllers;

use yii;
use yii\web\Controller;
use app\modules\api\models\SysConfig;
use app\modules\api\models\Season;


class BasicController extends Controller
{
    public $layout = false;

    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin:*');
        Yii::$app->params['lid'] = 1;
        $sysConfig = SysConfig::find()->select(['activity_name', 'exchange_code', 'begin_time', 'end_time', 'is_close', 'is_test'])->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->one();
        if(empty($sysConfig)){
        	return false;
        }
        if(time() < $sysConfig['begin_time']){
        	echo ShowRes(31001, '活动未开始');
        	return false;
        }
        if(time() > $sysConfig['end_time']){
        	echo ShowRes(31002, '活动已结束');
        	return false;
        }
        if($sysConfig['is_close'] == 1){
        	echo ShowRes(30040, '系统已关闭');
        	return false;
        }
        if($sysConfig['is_test'] == 1){
        	echo ShowRes(30041, '系统调试中');
        	return false;
        }

        /*验证是否在场次时间内*/
        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->one();
        if(empty($season)){
            echo ShowRes(31001, '活动未开始', []);
            return false;
        }


        return true;
    }


}
