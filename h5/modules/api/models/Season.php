<?php

namespace app\modules\api\models;

use Yii;

class Season extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%season}}';
    }

    public function rules()
    {
        return [
            ['season_name', 'required', 'message' => '场次名称不能为空'],
            ['season_name', 'string', 'max' => 32],
            ['luckydraw_begin_time', 'required', 'message' => '活动开始时间不能为空'],
            ['luckydraw_begin_time', 'integer', 'message' => '活动开始时间必须为正整数'],
            ['luckydraw_end_time', 'required', 'message' => '活动结束时间不能为空'],
            ['luckydraw_end_time', 'integer', 'message' => '活动结束时间必须为正整数'],
            ['lid', 'required', 'message' => 'lid不能为空'],
            ['lid', 'integer', 'message' => '应用ID必须为正整数'],
        ];
    }



}
