<?php

namespace app\modules\admin\models;

use Yii;

class SysConfig extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%sys_config}}';
    }

    public function rules()
    {
        return [
            ['activity_name', 'string', 'max' => 64],
            ['exchange_code', 'string', 'max' => 6],
            ['begin_time', 'required', 'message' => '活动开始时间不能为空'],
            ['begin_time', 'integer', 'message' => '活动开始时间必须为正整数'],
            ['end_time', 'required', 'message' => '活动结束时间不能为空'],
            ['end_time', 'integer', 'message' => '活动结束时间必须为正整数'],
            ['is_close', 'in', 'range' => [0, 1], 'message' => '是否关闭格式不正确'],
            ['is_test', 'in', 'range' => [0, 1], 'message' => '是否测试格式不正确'],
        ];
    }

    /*更新 系统配置*/
    public function set($data)
    {
        if(isset($data['SysConfig']['begin_time']) and !empty($data['SysConfig']['begin_time'])){
            $data['SysConfig']['begin_time'] = strtotime($data['SysConfig']['begin_time']);
        }
        if(isset($data['SysConfig']['end_time']) and !empty($data['SysConfig']['end_time'])){
            $data['SysConfig']['end_time'] = strtotime($data['SysConfig']['end_time']);
        }

        if($this->load($data) and $this->validate()){
            $sysConfig = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->one();
            if(is_null($sysConfig)){
               return false; 
            }
            $sysConfig->activity_name = $data['SysConfig']['activity_name'];
            $sysConfig->exchange_code = $data['SysConfig']['exchange_code'];
            $sysConfig->begin_time = $data['SysConfig']['begin_time'];
            $sysConfig->end_time = $data['SysConfig']['end_time'];
            $sysConfig->is_close = $data['SysConfig']['is_close'];
            $sysConfig->is_test = $data['SysConfig']['is_test'];
            if($sysConfig->save(false)){
                /*写入日志*/
                SysLog::addLog('修改系统参数成功');
                return true;
            }
        }
        return false;
    }


}
