<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class DrawLogSearch extends DrawLog
{
    public function rules()
    {
        return [
            [['sid'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $params['DrawLogSearch']['sid'] = (isset($params['sid']) and $params['sid']>0)?$params['sid']:'';
        // P($params);


        $query = DrawLog::find();

        if($this->load($params)){//没有加载成功
            // P($params);
        }

        if (!$this->validate()) {//没有通过验证
            P($params);
        }
        // P($params);

        $query->andFilterWhere([
            '{{%draw_log}}.sid' => $this->sid,
        ]);


        return $query;
    }
}
