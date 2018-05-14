<?php

namespace app\modules\admin\models;

use Yii;
use libs\AdminInfo;

class Admin extends \yii\db\ActiveRecord
{
    public $passwordConfirm;
    
    public static function tableName()
    {
        return '{{%admin}}';
    }

    public function rules()
    {
        return [
            ['username', 'required', 'message' => '账号不能为空', 'on' => ['login', 'addManager']],
            ['password', 'required', 'message' => '密码不能为空', 'on' => ['login', 'addManager']],
            ['passwordConfirm', 'required', 'message' => '重复密码不能为空', 'on' => 'addManager'],
            ['passwordConfirm', 'compare', 'compareAttribute' => 'password', 'message' => '两次密码不一致', 'on' => ['login', 'addManager']],
            ['role_id', 'integer', 'message' => '角色格式不正确', 'on' => ['addManager', 'modManager']],
            ['phone', 'string', 'max' => 20, 'on' => ['addManager', 'modManager']],
            ['email', 'email', 'message' => '邮箱格式不正确', 'on' => ['addManager', 'modManager']],
            ['realname', 'required', 'message' => '真实姓名不能为空', 'on' => ['addManager', 'modManager']],
            ['creater', 'string', 'max' => 20, 'on' => ['addManager', 'modManager']],
            ['state', 'in', 'range' => ['1', '2'],  'message' => '状态格式不正确', 'on' => ['addManager', 'modManager']],
            [['lastloginip', 'lastlogin_time', 'add_time', 'logintimes'], 'safe', 'on' => ['addManager', 'modManager']],
            ['password', 'validatePass', 'on' => 'login'],
            ['username', 'validateRepeat', 'on' => 'addManager'],
        ];
    }

    /*rules validatePass*/
    public function validatePass()
    {
        if(!$this->hasErrors()){
            $data = self::find()->where('username = :uname and password = :pass', [':uname' => $this->username, ':pass' => md5($this->password)])->one();
            if(is_null($data)){
                $this->addError('password', '用户名或密码错误');
            }
        }
    }

    /*rules validatePass*/
    public function validateRepeat()
    {
        if(!$this->hasErrors()){
            $data = self::find()->where('username = :uname', [':uname' => $this->username])->one();
            if($data){
                $this->addError('username', '该用户名已经存在');
            }
        }
    }

    /*验证登录*/
    public function login($data)
    {
        $this->scenario = 'login';
        if($this->load($data) and $this->validate()){//验证成功
            AdminInfo::setLoginInfo($this->username);//存入登录信息
            AdminInfo::setCompanyId(1);//存入companyid
            /*更新最后登录时间 和 登录次数*/
            $this->updateAll(['lastloginip' => ip2long(Yii::$app->request->userIP), 'lastlogin_time' => time()], 'username = :uname', [':uname' => $this->username]);
            $this->updateAllCounters(['logintimes' => 1], 'username = :uname', [':uname' => $this->username]);
            /*写入日志*/
            SysLog::addLog('成功登录系统');
            return true;
        }
        return false;
    }

    /*添加管理员*/
    public function addManager($data)
    {
        $this->scenario = 'addManager';
        $data['Admin']['creater'] = AdminInfo::getLoginName();
        if(isset($data['Admin']['password']) and !empty($data['Admin']['password'])){
            $data['Admin']['password'] = md5($data['Admin']['password']);
        }
        if(isset($data['Admin']['passwordConfirm']) and !empty($data['Admin']['passwordConfirm'])){
            $data['Admin']['passwordConfirm'] = md5($data['Admin']['passwordConfirm']);
        }
        $data['Admin']['add_time'] = time();
        if($this->load($data) and $this->validate()){
            if($this->save(false)){
                /*写入日志*/
                SysLog::addLog('添加管理员['. $data['Admin']['username'] .']成功');
                return true;
            }
        }
        return false;
    }

    /*修改管理员*/
    public function modManager($id, $data)
    {
        $this->scenario = 'modManager';
        if(isset($data['Admin']['password']) and !empty($data['Admin']['password'])){
            $data['Admin']['password'] = md5($data['Admin']['password']);
        }
        if(isset($data['Admin']['passwordConfirm']) and !empty($data['Admin']['passwordConfirm'])){
            $data['Admin']['passwordConfirm'] = md5($data['Admin']['passwordConfirm']);
        }
        if($this->load($data) and $this->validate()){
            if($data['Admin']['password'] != $data['Admin']['passwordConfirm']){
                $this->addError('password', '两次密码不一致');
                return false;
            }
            $manager = self::find()->where('user_id = :uid', [':uid' => $id])->one();
            if(is_null($manager)){
               return false; 
            }
            if($data['Admin']['password']){
                $manager->password = $data['Admin']['password'];
            }
            $manager->phone = $data['Admin']['phone'];
            $manager->email = $data['Admin']['email'];
            $manager->realname = $data['Admin']['realname'];
            $manager->state = $data['Admin']['state'];
            if($manager->save(false)){
                /*写入日志*/
                SysLog::addLog('修改管理员['. $manager->username .']成功');
                return true;
            }
            return false;
        }

    }
    
}
