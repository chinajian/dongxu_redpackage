<?php

namespace app\modules\admin\models;

use Yii;

class DrawLog extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%draw_log}}';
    }

    public function rules()
    {
        return [
            ['pid', 'required', 'message' => '奖品ID不能为空', 'on' => ['add']],
            ['pid', 'integer', 'message' => '奖品ID必须为正整数'],
            ['sid', 'required', 'message' => '场次ID不能为空', 'on' => ['add']],
            ['sid', 'integer', 'message' => '场次ID必须为正整数'],
            ['box_num', 'integer', 'message' => '盒子编码必须为正整数'],
            ['exchange_time', 'required', 'message' => '活动开始时间不能为空', 'on' => ['exchange']],
            ['exchange_time', 'integer', 'message' => '活动开始时间必须为正整数', 'on' => ['exchange']],
            ['draw_time', 'required', 'message' => '活动开始时间不能为空', 'on' => ['add']],
            ['draw_time', 'integer', 'message' => '活动开始时间必须为正整数', 'on' => ['add']],
            ['lid', 'required', 'message' => 'lid不能为空', 'on' => ['add']],
            ['lid', 'integer', 'message' => '应用ID必须为正整数'],
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
    
    
    /*关联查询 会员信息*/
    public function getUser()
    {
        $user = $this->hasOne(User::className(), ['uid' => 'uid']);
        return $user;
    }

}
