<?php

namespace app\modules\api\models;

use Yii;

class Ratio extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%ratio}}';
    }

    public function rules()
    {
        return [
            ['pid', 'required', 'message' => '奖品ID不能为空', 'on' => ['setAll']],
            ['pid', 'integer', 'message' => '奖品ID必须为正整数'],
            ['sid', 'required', 'message' => '场次ID不能为空', 'on' => ['setAll']],
            ['sid', 'integer', 'message' => '场次ID必须为正整数'],
            ['probability', 'required', 'message' => '概率不能为空', 'on' => ['setProbability']],
            ['probability', 'integer', 'max' => 10000, 'message' => '概率必须为正整数'],
            ['total_num', 'required', 'message' => '总数量不能为空', 'on' => ['setAll']],
            ['total_num', 'integer', 'max' => 60000, 'message' => '总数量必须为正整数'],
            ['out_num', 'integer', 'message' => '中奖数量必须为正整数'],
            ['lid', 'required', 'message' => 'lid不能为空'],
            ['lid', 'integer', 'message' => 'lid必须为正整数'],
        ];
    }


    /*关联查询 场次信息*/
    public function getSeason()
    {
        $season = $this->hasOne(Season::className(), ['sid' => 'sid'])->select(['sid', 'season_name', 'luckydraw_begin_time', 'luckydraw_end_time']);
        return $season;
    }

    /*关联查询 奖品信息*/
    public function getPrize()
    {
        $prize = $this->hasOne(Prize::className(), ['pid' => 'pid'])->select(['pid', 'prize_name', 'prize_img', 'is_red_packet', 'red_packet_money', 'is_thanks']);
        return $prize;
    }
    

}
