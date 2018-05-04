<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\Season;
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


}
