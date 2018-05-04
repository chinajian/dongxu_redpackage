<?php

namespace app\modules\admin\models;

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

    /*添加配比*/
    public function addRatio($data)
    {
        $this->scenario = 'setAll';
        $data['Ratio']['probability'] = 0;
        $data['Ratio']['out_num'] = 0;
        $data['Ratio']['lid'] = Yii::$app->params['lid'];

        if($this->load($data) and $this->validate()){
            /*验证场次是否存在*/
            $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid = :id', [':id' => $data['Ratio']['sid']])->asArray()->one();
            if(is_null($season)){
                $this->addError('sid', '场次不存在');
                return false;
            }

            /*验证奖品是否存在*/
            $prize = Prize::find()->where('lid = :lid or is_thanks = 1', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $data['Ratio']['pid']])->asArray()->one();
            if(is_null($prize)){
                $this->addError('pid', '奖品不存在');
                return false;
            }

            /*验证是否此奖品此场次，是否已经存在，不能重复*/
            $ratio = Ratio::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :pid', [':pid' => $data['Ratio']['pid']])->andWhere('sid = :sid', [':sid' => $data['Ratio']['sid']])->asArray()->one();
            if(!is_null($ratio)){
                $this->addError('pid', '此奖品已经存在');
                return false;
            }

            if($this->save(false)){
                /*写入日志*/
                SysLog::addLog('添加['. $prize['prize_name'] .']配比成功');
                return true;
            }
        }
        return false;
    }


    /*修改配比*/
    public function modRatio($id, $data)
    {
        $this->scenario = 'setAll';
        $data['Ratio']['lid'] = Yii::$app->params['lid'];
        if($this->load($data) and $this->validate()){
            // /*验证场次是否存在*/
            $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid = :id', [':id' => $data['Ratio']['sid']])->asArray()->one();
            if(is_null($season)){
                $this->addError('sid', '场次不存在');
                return false;
            }

            // /*验证奖品是否存在*/
            $prize = Prize::find()->where('lid = :lid or is_thanks = 1', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :id', [':id' => $data['Ratio']['pid']])->asArray()->one();
            if(is_null($prize)){
                $this->addError('pid', '奖品不存在');
                return false;
            }

            // /*验证是否此奖品此场次，是否已经存在，不能重复*/
            $ratio = Ratio::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('pid = :pid', [':pid' => $data['Ratio']['pid']])->andWhere('sid = :sid', [':sid' => $data['Ratio']['sid']])->andWhere('id <> :id', [':id' => $id])->asArray()->one();
            if(!is_null($ratio)){
                $this->addError('pid', '此奖品已经存在');
                return false;
            }

            $ratio = self::find()->joinWith('prize')->where('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('id = :id', [':id' => $id])->one();
            if(is_null($ratio)){
               return false; 
            }
            $ratio->pid = $data['Ratio']['pid'];
            $ratio->sid = $data['Ratio']['sid'];
            $ratio->total_num = $data['Ratio']['total_num'];
            if($ratio->save(false)){
                /*写入日志*/
                SysLog::addLog('修改配比['. $ratio->prize->prize_name .']成功');
                return true;
            }
            return false;
        }
    }


    /*
    设置配比
    $sid    场次id
    $data   概率数据
    */
    public function setProbability($sid, $data)
    {
        $this->scenario = 'setProbability';
        // P($data);
        /*循环取出奖品配比 并修改*/
        $num = 0;//所有概率的总和
        $set_zero = false;//如果所有概率总和大于10000，那么后面的奖品概率都将为0
        foreach($data['Ratio']['probability'] as $k =>$v){
            $ratio = self::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid = :sid', [':sid' => $data['Ratio']['sid']])->orderBy(['id' => SORT_ASC])->offset($k)->limit(1)->one();
            if(is_null($ratio)){
                return false;
            }

            /*得出概率，概率总和不能超过10000>>>*/
            if(!$set_zero){
                if($v>=10000 || ($v+$num)>=10000){
                    $data['Ratio']['probability'][$k] = 10000-$num;
                    $set_zero = true;
                }else{
                    $data['Ratio']['probability'][$k] = $v;
                }
                $num = $num + $v;
            }else{
                $data['Ratio']['probability'][$k] = 0;
            }
            /*得出概率，概率总和不能超过10000<<<*/
            $ratio->probability = $data['Ratio']['probability'][$k];
            if(!$ratio->save()){
                if($ratio->hasErrors()){
                    $this->addError('probability', $ratio->getErrors());
                }
                return false;
            }

            /*如果 比如此场次有3场，前端只传了2个数据，后面的概率，就讲变成0*/
            $this->updateAll(['probability' => 0], 'id>'.$ratio->id.' and sid = '.$sid);
        }
        return true;

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
