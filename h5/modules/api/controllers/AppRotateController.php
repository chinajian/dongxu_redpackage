<?php
namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use app\modules\api\controllers\BasicController;
use app\modules\api\models\Prize;
use app\modules\api\models\Season;
use app\modules\api\models\Ratio;
use app\modules\api\models\DrawLog;
use app\modules\api\models\SysConfig;
use app\modules\api\models\User;


class AppRotateController extends BasicController
{
    public $layout = false;

    /*
	获取奖品数据
	return array
    */
    public function actionGetPrize()
    {
	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if(!empty($season)){
    		/*取出此场次的奖品*/
    		$ratioList = Ratio::find()->select('{{%ratio}}.pid, {{%ratio}}.sid, {{%ratio}}.total_num, {{%ratio}}.out_num')->joinWith('prize')->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->andWhere('total_num>out_num')->orderBy(['id' => SORT_ASC])->asArray()->all();
    		// P($ratioList);
    		if(!empty($ratioList)){
    			return ShowRes(0, '', $ratioList);
    		}
    	}
    	return '';        
    }	

	
    public function actionSendRedpack()
    {
	$post = Yii::$app->request->post();
	//$post['openid'] = 'ow5AH1IlIW-GIHUhVjWENWwq0Mn8'; 
	//$post['nickname'] = '零度火焰'; 
	//$post['sex'] = '1';
	//$post['city'] = '常州'; 
	//$post['province'] = '江苏'; 
	//$post['country'] = '中国'; 
	//$post['headimgurl'] = 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIIbmW2JjyIGTSiamUFrl6uKeEjiaTIfib5tAbGVbqF8MlWgxk3zO0PcCtiby2A29ZiaThiaoAg9o0QZ4rA/132'; 
	$openid = isset($post['openid'])?$post['openid']:'';
	$nickname = isset($post['nickname'])?$post['nickname']:'';
	$sex = isset($post['sex'])?$post['sex']:'';
	$city = isset($post['city'])?$post['city']:'';
	$province = isset($post['province'])?$post['province']:'';
	$country = isset($post['country'])?$post['country']:'';
	$headimgurl = isset($post['headimgurl'])?$post['headimgurl']:'';
	if($openid and $nickname){
		//查询此openid是否已经存在，如果已经存在，就不需要发红包，否则随机发送一个红包
		$user = User::find() -> where('openid = :oid', [':oid' => $openid]) -> asArray() -> one();
		if(!$user){
			//增加会员信息
			$user = new User;
			$data = array(
				'User' => array(
					'openid' => $openid,
					'nickname' => json_encode($nickname),
					'sex' => $sex,
					'city'=> $city,
					'province' => $province,
					'country' => $country,
					'headimgurl' => $headimgurl
				)
			);
			if($user -> addUser($data)){
				$uid = $user->getPrimaryKey();
				/*取出当前场次的奖品*/
    				$prizeList = $this->currSeasonPrize();
				if(!empty($prizeList)){
					//P($prizeList);
					$prize = $this->luckyDrawByRandom($prizeList);//得出随机抽取的奖品
					/*整理中奖信息，存入数据库*/
					//P($prize);
    					$data = array(
    						'DrawLog' => array(
    							'pid' => $prize['pid'],
    							'sid' => $prize['sid'],
    							'uid' => $uid,
    							'lid' => Yii::$app->params['lid'],
    						)
    					);
    					$drawLogModel = new DrawLog;
					$transaction = Yii::$app->db->beginTransaction();//事物处理
					try{
	    					if(!$drawLogModel->addLog($data)){//存入抽奖日志
	               					if($drawLogModel->hasErrors()){
	                    					return ShowRes(30010, $drawLogModel->getErrors());
	                				}else{
	                    					return ShowRes(30000, '抽奖失败');
	                				}
	            				}

	            				/*更新已中奖的数量*/
	            				$ratio = Ratio::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('id = :id', [':id' => $prize['id']])->one();
	            				$ratio->updateAllCounters(['out_num' => 1], 'id = :id', [':id' => $prize['id']]);

	            				$transaction->commit();
						if($prize['prize']['is_red_packet']){//如果是微信红包的话
        						$res = $this->SetPrize($post['openid'], $prize['prize']['red_packet_money']);//发送微信红包
						}
					}catch(\Exception $e){
                				$transaction->rollback();
                				if(YII_DEBUG){
                    					return ShowRes(30020, '异常信息：'.$e->getMessage().'异常文件：'.$e->getFile().'异常所在行：'.$e->getLine().'异常码：'.$e->getCode());
                				}else{
                    					// throw new \Exception();
                   					return ShowRes(30020, '异常杯具');
                				}
               					return false;
            				};
				}
			}else{//添加会员失败
				$res = array(
					'return_code'=> -3
				);
			}
		}else{//已经存在此会员
			$res = array(
				'return_code'=> -2
			);
		}
	}else{
		$res = array(
			'return_code' => -1
		);
	};
	return json_encode($res);

    }



    /*
	取出当前场次的奖品
    */
    private function currSeasonPrize()
    {
    	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if(!empty($season)){
    		/*取出此场次的奖品*/
    		$ratioList = Ratio::find()->joinWith('prize')->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->andWhere('total_num>out_num')->orderBy(['id' => SORT_ASC])->asArray()->all();
    		// P($ratioList);
    		if(!empty($ratioList)){
    			return $ratioList;
    		}
    	}
    	return [];
    }


    /*
    随机抽取奖品
    $data 	奖品数据
    */
    private function luckyDrawByRandom($data)
    {
        $res = [];
        foreach ($data as $k => $v) {
            $tmpArr[$k] = $v['probability'];
        }
        // 概率数组的总概率
        $proSum = array_sum($tmpArr);
        if($proSum > 0){
	        asort($tmpArr);
	        // 概率数组循环
	        foreach ($tmpArr as $k => $v) {
	            $randNum = mt_rand(1, $proSum);
	            if ($randNum <= $v) {
	                $res = $data[$k];
	                break;
	            } else {
	                $proSum -= $v;
	            }
	        }
        }

        return $res;
    }



    /*发送微信红包*/
    private function SetPrize($openid, $amount)
    {
        $rtime = time();
        $mch_billno = '1491194952' . date("YmdHis", $rtime) . rand(1000, 9999);
        $nonce_str = md5($this -> getRandChar(10) . $rtime);
        $__construct = [
            "nonce_str" => $nonce_str,
            "mch_billno" => $mch_billno, //商户订单号
            "mch_id" =>  '1491194952', //商户号
            "wxappid" => 'wx821133e056b35771', //公众账号appid
            "send_name" => '东旭咨询服务', //商户名称
            "re_openid" => $openid, //用户openid
            "total_amount" => $amount, //付款金额，单位分
            "total_num" => 1, //红包发放总人数
            "wishing" => '注册送红包', //红包祝福语
            "client_ip" => '139.224.40.205', //调用接口的机器Ip地址
            "act_name" => '东旭有礼了', //活动名称
            "remark" => ''//备注信息
        ];
        ksort($__construct);
        $string = $this -> ToUrlParams($__construct);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . '344cecb07ed8464f7ffdd98a2861890d';
        //签名步骤三：MD5加密
        $string = md5($string);
        $__construct['sign'] = strtoupper($string);
        $xml = $this -> ToXml($__construct);
//        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = $this -> postXmlCurl($xml, "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack", true, 10);
        $result = $this -> FromXml($response);
	return $result;
    }


    /*
        返回随机字符串

    */
    private function getRandChar($length = 6)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    /*
        
    */
    private function ToUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");

        return $buff;
    }


    /**/
    private function ToXml($values)
    {
        if ( !is_array($values) || count($values) <= 0) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }

    /**/
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {

        //$weixin_info = \Yii::$app->params['weixin'];
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        /*if ($weixin_info['CURL_PROXY_HOST'] != "0.0.0.0" && $weixin_info['CURL_PROXY_PORT'] != 0) {
            curl_setopt($ch, CURLOPT_PROXY, $weixin_info['CURL_PROXY_HOST']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $weixin_info['CURL_PROXY_PORT']);
        }*/
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, '/www/wwwroot/redpacket.dongxulaowu.com/h5/cert/apiclient_cert.pem');
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, '/www/wwwroot/redpacket.dongxulaowu.com/h5/cert/apiclient_key.pem');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);

            return $data;
        } else {
            $error = curl_errno($ch);
            var_dump($error);
            curl_close($ch);

            return false;
            //throw new \WxPayException("curl出错，错误码:$error");
        }
    }


    /**/
    private function FromXml($xml)
    {
        if ( !$xml) {
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

}
