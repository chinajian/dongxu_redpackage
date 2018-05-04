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
use app\modules\admin\models\Prize;

class RatioController extends BasicController
{
    /**
     * 奖品配比列表
     */
    public function actionRatioList()
    {
        $get = Yii::$app->request->get();
        if(isset($get['page'])){
            $currPage = (int)$get['page']?$get['page']:1;
        }else{
            $currPage = 1;
        }
        $searchModel = (new RatioSearch())->search($get);
        $count = $searchModel->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->count();
        $pageSize = Yii::$app->params['pageSize'];
        $ratioList = $searchModel->joinWith('season')->joinWith('prize')->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->orderBy(['id' => SORT_ASC])->offset($pageSize*($currPage-1))->limit($pageSize)->asArray()->all();
        // P($ratioList);
        
        /*场次数据*/
        $seasonList = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($seasonList);

        return $this->render('ratioList', [
            'ratioList' => $ratioList,
            'seasonList' => $seasonList,
            'get' => $get,
            'pageInfo' => [
                'count' => $count,
                'currPage' => $currPage,
                'pageSize' => $pageSize,
            ]
        ]);
    }

    /**
     * 添加配比
     */
    public function actionAddRatio()
    {
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            // P($post);
            $ratioModel = new Ratio;
            if($ratioModel->addRatio($post)){
                return ShowRes(0, '添加成功', '', Url::to(['ratio/add-ratio']));
                Yii::$app->end();
            }else{
                if($ratioModel->hasErrors()){
                    return ShowRes(30010, $ratioModel->getErrors());
                }else{
                    return ShowRes(30000, '添加失败');
                }
            }
            return;
        }

        /*场次数据*/
        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($season);

        /*奖品数据*/
        $prize = Prize::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->orWhere(['is_thanks' => 1])->asArray()->all();
        // P($prize);

        return $this->render('addRatio',[
            'season' => $season,
            'prize' => $prize
        ]);
    }


    /*
    修改配比
    */
    public function actionModRatio()
    {
        /*如果有数据，进行修改*/
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            // P($post);
            $id = (int)(isset($post['Ratio']['id'])?$post['Ratio']['id']:0);
            if(!$id){
                return ShowRes(30030, '参数有误！');
                Yii::$app->end();
            }
            $ratioModel = new Ratio;
            if($ratioModel->modRatio($id, $post)){
                return ShowRes(0, '修改成功', '', 'back');
                Yii::$app->end();
            }else{
                if($ratioModel->hasErrors()){
                    return ShowRes(30010, $ratioModel->getErrors());
                }else{
                    return ShowRes(30000, '修改失败');
                }
            }
            return;
        }

        $get = Yii::$app->request->get();
        $id = (int)(isset($get['id'])?$get['id']:0);
        if(!$id){
            return ShowRes(30030, '参数有误！');
            Yii::$app->end();
        }

        /*场次数据*/
        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($season);

        /*奖品数据*/
        $prize = Prize::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->orWhere(['is_thanks' => 1])->asArray()->all();
        // P($prize);

        $ratio = Ratio::find()->joinWith('season')->joinWith('prize')->where('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('id = :id', [':id' => $id])->asArray()->one();
        // P($ratio);
        return $this->render('modRatio', [
            'season' => $season,
            'prize' => $prize,
            'ratio' => $ratio
        ]);

    }


    /*
    删除配比
    */
    public function actionDelRatio()
    {
        $post = Yii::$app->request->post();
        $id = (int)(isset($post['id'])?$post['id']:0);
        if(!$id){
            return ShowRes(30030, '参数有误！');
            Yii::$app->end();
        }
        $ratio = Ratio::find()->joinWith('prize')->where('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('id = :id', [':id' => $id])->one();
        if($ratio and $ratio->delete()){
            /*写入日志*/
            SysLog::addLog('删除配比['. $ratio->prize->prize_name .']成功');

            return ShowRes(0, '删除成功', '', Url::to(['ratio/ratio-list']));
            Yii::$app->end();
        }else{
            return ShowRes(30000, '删除失败');
            Yii::$app->end();
        }
    }


}
