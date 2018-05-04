<?php

namespace app\modules\api\models;

use Yii;

class Prize extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%prize}}';
    }

    public function rules()
    {
        return [
            ['prize_name', 'required', 'message' => '奖品名称不能为空'],
            ['prize_name', 'string', 'max' => 32],
            ['prize_img', 'string', 'max' => 1024],
            ['is_red_packet', 'in', 'range' => [0, 1], 'message' => '是否是微信红包格式不正确'],
            ['red_packet_money', 'integer', 'message' => '红包金额必须为正整数'],
            ['lid', 'required', 'message' => 'lid不能为空'],
            ['lid', 'integer', 'message' => 'lid必须为正整数'],
        ];
    }

}
