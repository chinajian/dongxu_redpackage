<?php

namespace app\modules\admin\models;

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

    /*
    更新 抽奖场次
    $index  更新第几条数据
    */
    public function set($data, $index)
    {
        /*根据索引，取出对应的价格区间*/
        $season = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->orderBy(['sid' => SORT_ASC])->offset($index)->limit(1)->one();

        if(!empty($season)){
            if(isset($data['Season']['luckydraw_begin_time']) and !empty($data['Season']['luckydraw_begin_time'])){
                $data['Season']['luckydraw_begin_time'] = strtotime($data['Season']['luckydraw_begin_time']);
            }else{
                unset($data['Season']['luckydraw_begin_time']);
            }
            if(isset($data['Season']['luckydraw_end_time']) and !empty($data['Season']['luckydraw_end_time'])){
                $data['Season']['luckydraw_end_time'] = strtotime($data['Season']['luckydraw_end_time']);
            }else{
                unset($data['Season']['luckydraw_end_time']);
            }
            if($index){//必须要第二个才需要判断，第一个不需要判断
                /*查询上一条数据，为了判断是否跟上一条数据，时间是否重合，必须要在上一条数据的时间下面*/
                $seasonPrev = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->offset($index-1)->limit(1)->one();
                if($seasonPrev->luckydraw_end_time > $data['Season']['luckydraw_begin_time']){
                    $this->addError('luckydraw_begin_time', '不能早于上一个场次');
                    return false;
                }
            }

            /*修改数据*/
            $season->season_name = $data['Season']['season_name'];
            $season->luckydraw_begin_time = $data['Season']['luckydraw_begin_time'];
            $season->luckydraw_end_time = $data['Season']['luckydraw_end_time'];
            if($season->validate()){
                if($season->save(false)){
                    return true;
                }
            }else{
                $this->addError('season_name', $season->getErrors());
                return false;
            }

        }else{
            /*说明之前没有，需要添加新的数据*/
            if($this->add($data)){
                return true;
            }
        }
    }


    /*增加场次*/
    public function add($data)
    {
        if(isset($data['Season']['luckydraw_begin_time']) and !empty($data['Season']['luckydraw_begin_time'])){
            $data['Season']['luckydraw_begin_time'] = strtotime($data['Season']['luckydraw_begin_time']);
        }else{
            unset($data['Season']['luckydraw_begin_time']);
        }
        if(isset($data['Season']['luckydraw_end_time']) and !empty($data['Season']['luckydraw_end_time'])){
            $data['Season']['luckydraw_end_time'] = strtotime($data['Season']['luckydraw_end_time']);
        }else{
            unset($data['Season']['luckydraw_end_time']);
        }
        // P($data);

        /*查询上一条数据，为了判断是否跟上一条数据，时间是否重合，必须要在上一条数据的时间下面*/
        $seasonPrev = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->orderBy(['sid' => SORT_DESC])->one();
        if($seasonPrev->luckydraw_end_time > $data['Season']['luckydraw_begin_time']){
            $this->addError('luckydraw_begin_time', '不能早于上一个场次');
            return false;
        }
        
        if($this->load($data) and $this->validate()){
            if($this->save(false)){
                return true;
            }
        }
        return false;

    }


}
