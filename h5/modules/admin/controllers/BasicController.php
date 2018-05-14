<?php

namespace app\modules\admin\controllers;

use yii;
use yii\web\Controller;
use app\models\Album;
use libs\AdminInfo;


class BasicController extends Controller
{
    public $layout = 'default';

    public function beforeAction($action)
    {
        /*验证登录*/
        if(!AdminInfo::getIsLogin()){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        
        /*
        验证登录
        此处应该到Login 应用中读取
        */
        $this->view->params['adminIoginInfo'] = [
            'mid' => 11,
            'mname' => 'supermanage',
            'accessAppID' => '1',//
            'menu' => [
                array(
                    "name" => '系统设置',
                    "icon" => 'cog',
                    "m" => 'Admin',
                    "c" => 'sys_setup',
                    "a" => 'index',
                    "data" => '',
                    "children" => [
                        array(
                            "name" => '系统设置',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'sys_setup',
                            "a" => 'index',
                            "data" => '',
                        ),array(
                            "name" => '场次设置',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'season',
                            "a" => 'index',
                            "data" => '',
                        ),array(
                            "name" => '操作日志',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'sys_log',
                            "a" => 'index',
                            "data" => '',
                        ),
                    ]
                ),
                array(
                    "name" => '奖品管理',
                    "icon" => 'gift',
                    "m" => 'Admin',
                    "c" => 'prize',
                    "a" => 'prize-list',
                    "data" => '',
                    "children" => [
                        array(
                            "name" => '奖品列表',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'prize',
                            "a" => 'prize-list',
                            "data" => '',
                        ),array(
                            "name" => '奖品配比',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'ratio',
                            "a" => 'ratio-list',
                            "data" => '',
                        ),array(
                            "name" => '概率设置',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'probability',
                            "a" => 'index',
                            "data" => '',
                        )
                    ]
                ),
                array(
                    "name" => '抽奖数据',
                    "icon" => 'hdd',
                    "m" => 'Admin',
                    "c" => 'lucky',
                    "a" => 'lucky-list',
                    "data" => '',
                    "children" => [
                        array(
                            "name" => '获奖列表',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'lucky',
                            "a" => 'lucky-list',
                            "data" => '',
                        ),array(
                            "name" => '抽奖日志',
                            "icon" => '',
                            "m" => 'Admin',
                            "c" => 'draw',
                            "a" => 'draw-log',
                            "data" => '',
                        )
                    ]
                ),
            ],//能否访问的菜单，相当于权限内能否访问的菜单
            'isLogin' => 0,
        ];

        if(!isset($this->view->params['adminIoginInfo']['isLogin']) and $this->view->params['adminIoginInfo']['isLogin'] === 0){
            //未登录
            Yii::$app->end();
        }

        if(isset($this->view->params['adminIoginInfo']['accessAppID']) and !empty($this->view->params['adminIoginInfo']['accessAppID'])){
            //取出当前使用的appid>>>
            if(is_string($this->view->params['adminIoginInfo']['accessAppID'])){
                Yii::$app->params['lid'] = $this->view->params['adminIoginInfo']['accessAppID'];
            }
            if(is_array($this->view->params['adminIoginInfo']['accessAppID'])){
                Yii::$app->params['lid'] = $this->view->params['adminIoginInfo']['accessAppID'][0];//此处用的是缩影的第一条数据，本应该是给用户选择的
            }

            /*取出菜单*/
            $this->view->params['menu'] = $this->view->params['adminIoginInfo']['menu'];
        }else{

        }
        // echo Yii::$app->params['lid'];

        return true;
    }


    /*图片上传*/
    public function actionUploadFile()
    {
        $post = Yii::$app->request->post();
        // P($post);
        if($_FILES and $post['name']){//有图片上传
            $albumModel = new Album();
            if($fileName = $albumModel->upload($post)){
                return ShowRes(0, $fileName);
            }else{
                return ShowRes(30010, $albumModel->getErrors());
            }
            return;
        }
        return ShowRes(30000, '没有传图片！');
    }

}
