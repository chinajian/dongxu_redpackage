<?php
namespace libs;
use Yii;
/*
系统信息存取类
*/
class WapshopInfo
{
    private static $mode = 'seesion';//存储介质
	private static $lifetime = 3600;//存储时间

	private static function setMode()
	{
        self::$mode = Yii::$app->params['saveMode'];
    }

	/*保存登录信息*/
    public static function setLoginInfo($data)
    {
    	self::setMode();
        $lifetime = self::$lifetime;
    	if(self::$mode == 'seesion'){
	        $session = Yii::$app->session;
	        session_set_cookie_params($lifetime);
	        $session['wapshop'] = [
                'user_id' => $data['user_id'],
                'nickname' => $data['wechat_nickname'],
                'headimgurl' => $data['wechat_headimgurl'],
	            'openid' => $data['wechat_openid'],
	            'isLogin' => 1,
	        ];
    	}
    }


    /*是否登录*/
    public static function getIsLogin()
    {
    	self::setMode();
    	if(self::$mode == 'seesion'){
	    	$session = Yii::$app->session;
	    	if(isset($session['wapshop']['isLogin']) and $session['wapshop']['isLogin'] == 1){
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
        return 1;
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


    /*取出user_id*/
    public static function getUserId()
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            if(isset($session['wapshop']['user_id'])){
                return $session['wapshop']['user_id'];
            };
        }
        return "";
    }

    /*取出openid*/
    public static function getOpenid()
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            if(isset($session['wapshop']['openid'])){
                return urldecode($session['wapshop']['openid']);
            };
        }
        return "";
    }

    /*取出nickname*/
    public static function getNickname()
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            if(isset($session['wapshop']['nickname'])){
                return urldecode($session['wapshop']['nickname']);
            };
        }
        return "";
    }

    /*取出headimgurl*/
    public static function getHeadimgurl()
    {
        self::setMode();
        if(self::$mode == 'seesion'){
            $session = Yii::$app->session;
            if(isset($session['wapshop']['headimgurl'])){
                return $session['wapshop']['headimgurl'];
            };
        }
        return "";
    }


}