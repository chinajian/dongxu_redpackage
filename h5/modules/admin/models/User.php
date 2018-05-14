<?php

namespace app\modules\admin\models;

use Yii;

class User extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            ['openid', 'required', 'message' => 'openid不能为空'],
            ['openid', 'string', 'max'=>512],
            ['nickname', 'required', 'message' => 'nickname不能为空'],
            ['nickname', 'string', 'max'=>512],
            ['sex', 'integer', 'message' => '性别格式正确'],
            ['city', 'string', 'max'=>64],
            ['province', 'string', 'max'=>64],
            ['country', 'string', 'max'=>64],
            ['headimgurl', 'string', 'max'=>1024],
            ['add_time', 'safe'],
        ];
    }

    

    
    

}
