<?php

namespace app\modules\api\models;

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


}
