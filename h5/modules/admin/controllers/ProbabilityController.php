<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\SysLog;
use app\modules\admin\models\Ratio;
use app\modules\admin\models\RatioSearch;
use app\modules\admin\models\Season;

class ProbabilityController extends BasicController
{
    /**
     * 奖品配比列表
     */
    public function actionIndex()
    {
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            // P($post);
            $sid = (int)(isset($post['Ratio']['sid'])?$post['Ratio']['sid']:0);
            if(!$sid){
                return ShowRes(30030, '参数有误！');
                Yii::$app->end();
            }
            $ratioModel = new Ratio;
            if($ratioModel->setProbability($sid, $post)){
                return ShowRes(0, '设置成功', '', 'refresh');
                Yii::$app->end();
            }else{
                if($ratioModel->hasErrors()){
                    return ShowRes(30010, $ratioModel->getErrors());
                }else{
                    return ShowRes(30000, '设置失败');
                }
            }
            return;
        }

        $get = Yii::$app->request->get();

        /*场次数据*/
        $seasonList = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($seasonList);

        if(isset($get['sid']) and $get['sid']){
            $searchModel = (new RatioSearch())->search($get);
            $pageSize = Yii::$app->params['pageSize'];
            $ratioList = $searchModel->joinWith('season')->joinWith('prize')->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->orderBy(['id' => SORT_ASC])->asArray()->all();
            // P($ratioList);
        }else{//如果没有场次的话，就不要取出配比数据了
            $ratioList = [];
        }
        

        return $this->render('index', [
            'ratioList' => $ratioList,
            'seasonList' => $seasonList,
            'get' => $get
        ]);
    }


}
