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
use app\modules\admin\models\User;

class DrawController extends BasicController
{
    /**
     * 奖品配比列表
     */
    public function actionDrawLog()
    {
        $get = Yii::$app->request->get();
        if(isset($get['page'])){
            $currPage = (int)$get['page']?$get['page']:1;
        }else{
            $currPage = 1;
        }
        $searchModel = (new DrawLogSearch())->search($get);
        $count = $searchModel->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->count();
        $pageSize = Yii::$app->params['pageSize'];
        $drawLogList = $searchModel->joinWith('prize')->joinWith('user')->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->orderBy(['id' => SORT_ASC])->offset($pageSize*($currPage-1))->limit($pageSize)->asArray()->all();
         //P($drawLogList);
        
        /*场次数据*/
        $seasonList = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($seasonList);

        return $this->render('drawLogList', [
            'drawLogList' => $drawLogList,
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
