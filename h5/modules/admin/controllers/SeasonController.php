<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\Season;
use app\modules\admin\models\Ratio;
use app\modules\admin\models\SysLog;


class SeasonController extends BasicController
{
    /**
     * 抽奖场次设置
     */
    public function actionIndex()
    {
    	/*如果有数据，进行修改*/
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
           // P($post);
            $transaction = Yii::$app->db->beginTransaction();//事物处理
            try{
                if(isset($post['Season']['season_name'])){
                    foreach($post['Season']['season_name'] as $k => $v){
                        $seasonModel = new Season;
                        $newData['Season'] = '';
                        $newData['Season']['season_name'] = $post['Season']['season_name'][$k];
                        $newData['Season']['is_rotate'] = isset($post['Season']['is_rotate'][$k])?$post['Season']['is_rotate'][$k]:0;
                        $newData['Season']['luckydraw_begin_time'] = $post['Season']['luckydraw_begin_time'][$k];
                        $newData['Season']['luckydraw_end_time'] = $post['Season']['luckydraw_end_time'][$k];
                        $newData['Season']['lid'] = Yii::$app->params['lid'];
                        if(!$seasonModel->set($newData, $k)){
                            if($seasonModel->hasErrors()){
                                return ShowRes(30010, $seasonModel->getErrors());
                            }else{
                                return ShowRes(30000, '修改失败');
                            }
                        }
                    }

                    /*
                    删除之前多余的场次，比如之前有3条记录，现在只有2条记录
                    */
                    $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->offset($k+1)->all();
                    foreach($season as $k => $v){
                        $season[$k]->delete();
                    }

                }

                $transaction->commit();
                /*写入日志*/
                SysLog::addLog('设置场次成功');
                return ShowRes(0, '设置成功');
            }catch(\Exception $e){
                $transaction->rollback();
                if(YII_DEBUG){
                    return ShowRes(30020, '异常信息：'.$e->getMessage().'异常文件：'.$e->getFile().'异常所在行：'.$e->getLine().'异常码：'.$e->getCode());
                }else{
                    // throw new \Exception();
                    return ShowRes(30020, '异常杯具');
                }
                return false;
            };
            return;
        }

        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
        // P($season);
    	return $this->render('index', [
            'season' => $season
        ]);

    }

	/*
		删除一个场次
		@param int $id 场次ID
		@return bool 操作成功或失败
	*/
	public function actionDelSeason()
    {
    	$post = Yii::$app->request->post();
        $id = (int)(isset($post['id'])?$post['id']:0);
        if(!$id){
            return ShowRes(30030, '参数有误！');
            Yii::$app->end();
        }

		/*如果配比列表中已经存在，则不能删除*/
        $ratio = Ratio::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid = :id', [':id' => $id])->one();
		if($ratio){
            return ShowRes(30000, '配比中存在此奖品，不能删除！');
            Yii::$app->end();
        }

        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid = :id', [':id' => $id])->one();
        if($season and $season->delete()){
            /*写入日志*/
            SysLog::addLog('删除场次['. $season->season_name .']成功');

            return ShowRes(0, '删除成功', '', Url::to(['season/index']));
            Yii::$app->end();
        }else{
            return ShowRes(30000, '删除失败');
            Yii::$app->end();
        }
    }



}
