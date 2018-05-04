<?php

namespace app\modules\admin\models;

use Yii;


class SysLog extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%sys_log}}';
    }

    public function rules()
    {
        return [
            // ['mid', 'required', 'message' => '用户ID不能为空'],
            ['mid', 'integer', 'message' => '用户ID数据类型不正确'],
            // ['mname', 'required', 'message' => '用户名不能为空'],
            ['mname', 'string', 'max' => 64],
            // ['login_add', 'required', 'message' => '登录地址不能为空'],
            ['login_ip', 'required', 'message' => '登录IP不能为空'],
            ['content', 'required', 'message' => '日志内容不能为空'],
            ['content', 'string', 'max' => 300],
            ['operate_time', 'safe'],
            ['lid', 'required', 'message' => 'lid不能为空'],
            ['lid', 'integer', 'message' => 'lid必须为正整数'],
        ];
    }

    /*添加系统日志*/
    public static function addLog($content='')
    {
        $syslogModel = new SysLog();
        $syslogModel->mid = 0;
        $syslogModel->mname = '';
        $syslogModel->login_add = '';
        $syslogModel->login_ip = ip2long(Yii::$app->request->userIP);
        $syslogModel->content = $content;
        $syslogModel->operate_time = time();
        $syslogModel->lid = Yii::$app->params['lid'];
        if($syslogModel->validate()){
            if($syslogModel->save(false)){
                return true;
            }
        }
        return false;
    }

}
