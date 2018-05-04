<?php

namespace app\modules\admin\models;

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

    /*添加奖品*/
    public function addPrize($data)
    {
        if(isset($data['Prize']['prize_img']) and !empty($data['Prize']['prize_img'])){
            $data['Prize']['prize_img'] = implode(',', $data['Prize']['prize_img']);
        }
        if(!isset($data['Prize']['red_packet_money']) or empty($data['Prize']['red_packet_money'])){
            $data['Prize']['red_packet_money'] = 0;
        }
        $data['Prize']['lid'] = Yii::$app->params['lid'];
        if($this->load($data) and $this->validate()){
            if($this->save(false)){
                /*写入日志*/
                SysLog::addLog('添加奖品['. $data['Prize']['prize_name'] .']成功');
                return true;
            }
        }
        return false;
    }

    /*修改奖品*/
    public function modPrize($id, $data)
    {
        if(isset($data['Prize']['prize_img']) and !empty($data['Prize']['prize_img'])){
            $data['Prize']['prize_img'] = implode(',', $data['Prize']['prize_img']);
        }else{
            $data['Prize']['prize_img'] = '';
        }
        if(!isset($data['Prize']['red_packet_money']) or empty($data['Prize']['red_packet_money'])){
            $data['Prize']['red_packet_money'] = 0;
        }
        $data['Prize']['lid'] = Yii::$app->params['lid'];
        if($this->load($data) and $this->validate()){
            $prize = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $id])->one();
            if(is_null($prize)){
               return false; 
            }
            $prize->prize_name = $data['Prize']['prize_name'];
            $prize->prize_img = $data['Prize']['prize_img'];
            $prize->is_red_packet = $data['Prize']['is_red_packet'];
            $prize->red_packet_money = $data['Prize']['red_packet_money'];
            $prize->lid = $data['Prize']['lid'];
            if($prize->save(false)){
                /*写入日志*/
                SysLog::addLog('修改奖品['. $prize->prize_name .']成功');
                return true;
            }
            return false;
        }
    }

}
