<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\SysLog;
use app\modules\admin\models\Prize;
use app\modules\admin\models\Ratio;

class PrizeController extends BasicController
{
    /**
     * 奖品列表
     */
    public function actionPrizeList()
    {
        $get = Yii::$app->request->get();
        if(isset($get['page'])){
            $currPage = (int)$get['page']?$get['page']:1;
        }else{
            $currPage = 1;
        }
        $prizeModel = Prize::find();
        $count = $prizeModel->count();
        $pageSize = Yii::$app->params['pageSize'];
        $prizeList = $prizeModel->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->offset($pageSize*($currPage-1))->limit($pageSize)->asArray()->all();
        // P($prizeList);
        return $this->render('prizeList', [
            'prizeList' => $prizeList,
            'pageInfo' => [
                'count' => $count,
                'currPage' => $currPage,
                'pageSize' => $pageSize,
            ]
        ]);
    }

    /**
     * 添加奖品
     */
    public function actionAddPrize()
    {
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            // P($post);
            $prizeModel = new Prize;
            if($prizeModel->addPrize($post)){
                return ShowRes(0, '添加成功', '', Url::to(['prize/add-prize']));
                Yii::$app->end();
            }else{
                if($prizeModel->hasErrors()){
                    return ShowRes(30010, $prizeModel->getErrors());
                }else{
                    return ShowRes(30000, '添加失败');
                }
            }
            return;
        }
        return $this->render('addPrize');
    }

    /*
    修改奖品
    */
    public function actionModPrize()
    {
        /*如果有数据，进行修改*/
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $id = (int)(isset($post['Prize']['pid'])?$post['Prize']['pid']:0);
            if(!$id){
                return ShowRes(30030, '参数有误！');
                Yii::$app->end();
            }
            $prizeModel = new Prize;
            if($prizeModel->modPrize($id, $post)){
                return ShowRes(0, '修改成功', '', 'back');
                Yii::$app->end();
            }else{
                if($prizeModel->hasErrors()){
                    return ShowRes(30010, $prizeModel->getErrors());
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

        $prize = Prize::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $id])->asArray()->one();
        $prize['prize_img'] = !empty($prize['prize_img'])?explode(',', $prize['prize_img']):[];
        // P($prize);
        return $this->render('modPrize', [
            'prize' => $prize
        ]);

    }

    /*
	删除奖品
    */
    public function actionDelPrize()
    {
    	$post = Yii::$app->request->post();
        $id = (int)(isset($post['id'])?$post['id']:0);
        if(!$id){
            return ShowRes(30030, '参数有误！');
            Yii::$app->end();
        }
        if($id == 0){//如果删除 谢谢参与，不能删除
            return ShowRes(30031, '系统删除，不能删除！');
            Yii::$app->end();
        }

        /*如果配比列表中已经存在，则不能删除*/
        $ratio = Ratio::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $id])->one();
        if($ratio){
            return ShowRes(30000, '配比中存在此奖品，不能删除！');
            Yii::$app->end();
        }

        $prize = Prize::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $id])->one();
        if($prize and $prize->delete()){
            /*写入日志*/
            SysLog::addLog('删除奖品['. $prize->prize_name .']成功');

            return ShowRes(0, '删除成功', '', Url::to(['prize/prize-list']));
            Yii::$app->end();
        }else{
            return ShowRes(30000, '删除失败');
            Yii::$app->end();
        }
    }


}
