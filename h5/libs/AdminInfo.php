<?php
namespace libs;
use Yii;
/*
系统信息存取类
*/
class AdminInfo
{
    private static $mode = 'seesion';//存储介质
	private static $lifetime = 3600;//存储时间

	private static function setMode()
	{
        self::$mode = Yii::$app->params['saveMode'];
    }

	/*保存登录信息*/
    public static function setLoginInfo($loginName)
    {
    	self::setMode();
        $lifetime = self::$lifetime;
    	if(self::$mode == 'seesion'){
	        $session = Yii::$app->session;
	        session_set_cookie_params($lifetime);
	        $session['admin'] = [
	            'username' => $loginName,
	            'isLogin' => 1,
	        ];
    	}
    }

    /*清除登录信息*/
    public static function clearLoginInfo()
    {
    	self::setMode();
    	if(self::$mode == 'seesion'){
	    	Yii::$app->session->removeAll();
    	}
    }


    /*取出登录名*/
    public static function getLoginName()
    {
    	self::setMode();
    	if(self::$mode == 'seesion'){
	    	$session = Yii::$app->session;
	    	if(isset($session['admin']['username'])){
	            return $session['admin']['username'];
	        };
    	}
        return "";
    }

    /*是否登录*/
    public static function getIsLogin()
    {
    	self::setMode();
    	if(self::$mode == 'seesion'){
	    	$session = Yii::$app->session;
	    	if(isset($session['admin']['isLogin']) and $session['admin']['isLogin'] == 1){
	            return true;
	        };
    	}
        return false;
    }

    /*保存companyid*/
    public static function setCompanyId($loginName)
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            $session['companyid'] = 1;
        }
        return false;
    }

    /*取出companyid*/
    public static function getCompanyId()
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            if(isset($session['companyid'])){
                // return $session['companyid'];
                return 1;
            };
        }
        return "";
    }

}