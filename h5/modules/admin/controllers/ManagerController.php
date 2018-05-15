<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;
use app\modules\admin\models\Admin;
use app\modules\admin\models\SysLog;


class ManagerController extends BasicController
{
    /**
     * 管理员列表
     */
    public function actionManagerList()
    {
        $get = Yii::$app->request->get();
        if(isset($get['page'])){
            $currPage = (int)$get['page']?$get['page']:1;
        }else{
            $currPage = 1;
        }
    	$managerModel = Admin::find();
        $count = $managerModel->count();
        $pageSize = Yii::$app->params['pageSize'];
        $managerList = $managerModel->offset($pageSize*($currPage-1))->limit($pageSize)->all();
    	return $this->render('managerList', [
            'managerList' => $managerList,
    		'pageInfo' => [
                'count' => $count,
                'currPage' => $currPage,
                'pageSize' => $pageSize,
            ]
    	]);
    }

    /**
     * 添加管理员
     */
    public function actionAddManager()
    {
    	if(Yii::$app->request->isPost){
    		$post = Yii::$app->request->post();
    		$adminModel = new Admin;
    		if($adminModel->addManager($post)){
                return showRes(200, '添加成功', Url::to(['manager/manager-list']));
                Yii::$app->end();
            }else{
                if($adminModel->hasErrors()){
                    return showRes(300, $adminModel->getErrors());
                }else{
                    return showRes(300, '添加失败');
                }
            }
    		return;
    	}
        return $this->render('addManager');
    }

    /*
    修改管理员
    */
    public function actionModManager()
    {
        /*如果有数据，进行修改*/
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $id = (int)(isset($post['Admin']['user_id'])?$post['Admin']['user_id']:0);
            if(!$id){
                return showRes(300, '参数有误！');
                Yii::$app->end();
            }
            $adminModel = new Admin;
            if($adminModel->modManager($id, $post)){
                return showRes(200, '修改成功', Url::to(['manager/manager-list']));
                Yii::$app->end();
            }else{
                if($adminModel->hasErrors()){
                    return showRes(300, $adminModel->getErrors());
                }else{
                    return showRes(300, '修改失败');
                }
            }
            return;
        }

        $get = Yii::$app->request->get();
        $id = (int)(isset($get['id'])?$get['id']:0);
        if(!$id){
            return showRes(300, '参数有误！');
            Yii::$app->end();
        }
        $manager = Admin::find()->where('user_id = :id', [':id' => $id])->asArray()->one();
    	return $this->render('modManager', [
            'manager' => $manager
        ]);

    }

    /*
	删除管理员
    */
    public function actionDelManager()
    {
    	$post = Yii::$app->request->post();
    	$id = (int)(isset($post['id'])?$post['id']:0);
    	if(!$id){
    		return showRes(300, '参数有误！');
    		Yii::$app->end();
    	}
        if($id == 1){
            return showRes(300, '超级管理员不能删除！');
            Yii::$app->end();
        }
        $manager = Admin::find()->where('user_id = :id', [':id' => $id])->one();
    	if($manager and $manager->delete()){
            /*写入日志*/
            SysLog::addLog('删除管理员['. $manager->username .']成功');

    		return showRes(200, '删除成功', Url::to(['manager/manager-list']));
            Yii::$app->end();
    	}else{
    		return showRes(300, '删除失败');
            Yii::$app->end();
    	}
    }


}
