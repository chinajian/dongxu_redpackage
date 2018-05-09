<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\SysLog;
use app\modules\admin\models\DrawLog;
use app\modules\admin\models\DrawLogSearch;
use app\modules\admin\models\Season;
use app\modules\admin\models\Prize;

class LuckyController extends BasicController
{
    /**
     * 中奖日志列表
     */
    public function actionLuckyList()
    {
        $get = Yii::$app->request->get();
        if(isset($get['page'])){
            $currPage = (int)$get['page']?$get['page']:1;
        }else{
            $currPage = 1;
        }
        $searchModel = (new DrawLogSearch())->search($get);
        $count = $searchModel->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('{{%draw_log}}.pid!=1')->count();
        $pageSize = Yii::$app->params['pageSize'];
        $luckyList = $searchModel->joinWith('prize')->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('{{%draw_log}}.pid!=1')->orderBy(['id' => SORT_ASC])->offset($pageSize*($currPage-1))->limit($pageSize)->asArray()->all();
        // P($luckyList);
        
        /*场次数据*/
        $seasonList = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($seasonList);

        return $this->render('luckyList', [
            'luckyList' => $luckyList,
            'seasonList' => $seasonList,
            'get' => $get,
            'pageInfo' => [
                'count' => $count,
                'currPage' => $currPage,
                'pageSize' => $pageSize,
            ]
        ]);
    }



}