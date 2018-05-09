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


class AppController extends BasicController
{
    /*系统设置*/
    public function actionSysInfo()
    {
    	header('Access-Control-Allow-Origin:*');
        $sysConfig = SysConfig::find()->select(['activity_name', 'exchange_code', 'begin_time', 'end_time', 'is_close', 'is_test'])->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->one();
        if(!empty($sysConfig)){
            $sysConfig['begin_time'] = date('Y-m-d H:i:s', $sysConfig['begin_time']);
            $sysConfig['end_time'] = date('Y-m-d H:i:s', $sysConfig['end_time']);
            $sysConfig['is_close'] = $sysConfig['is_close']?'true':'false';
            $sysConfig['is_test'] = $sysConfig['is_test']?'true':'false';
        }
        // P($sysConfig);
        return ShowRes(0, '', $sysConfig);
    }




    /*
    取出所有奖品信息
    主要是为了前端先把奖品图片加载好
    */
    public function actionIndex()
    {
p('ddddd');
        header('Access-Control-Allow-Origin:*');
    	$prizeList = Prize::find()->select(['prize_name', 'prize_img'])->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->asArray()->all();
    	if(!empty($prizeList)){
	    	foreach($prizeList as $k => $v){
	    		$prize_img = explode(',', $v['prize_img']);
	    		if(isset($prize_img[0])){
	    			$prizeList[$k]['min_img'] = GetImgBySize($prize_img[0]);
	    		}else{
	    			$prizeList[$k]['min_img'] = '';
	    		}
	    		if(isset($prize_img[1])){
	    			$prizeList[$k]['big_img'] = GetImgBySize($prize_img[1], 'big');
	    		}else{
	    			$prizeList[$k]['big_img'] = '';
	    		}
	    		unset($prizeList[$k]['prize_img']);
	    	}
    	}

    	/*倒计时关闭，弹窗关闭, 全部翻牌关闭*/
	    $this->actionWriteRecord(0, 0);
	    $this->actionWriteRecord(1, 0);
	    $this->actionWriteRecord(2, 0);

    	// P($prizeList);
    	return ShowRes(0, '', $prizeList);
    }


    /*获取当前的场次信息*/
    public function actionGetCurrSeason()
    {
    	header('Access-Control-Allow-Origin:*');
    	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->select('luckydraw_begin_time')->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if($season){
    		return ShowRes(0, '', date('Y-m-d H:i', $season['luckydraw_begin_time']));
    	};
    	return ShowRes(0, '', '');
    }


    /*
    取出当前场次已经中出去的奖品
    */
    public function actionOutPrize()
    {
    	header('Access-Control-Allow-Origin:*');
    	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if(!empty($season)){
    		/*取出此场次 已经中出去的奖品*/
    		$drawLogList = DrawLog::find()->joinWith('prize')->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->orderBy(['draw_time' => SORT_DESC])->asArray()->all();
    		// P($drawLogList);
    		$res = [];//返回的数据
    		if(!empty($drawLogList)){
    			foreach($drawLogList as $k => $v){
    				$res[$k]['prize_name'] = $v['prize']['prize_name'];
    				$prize_img = explode(',', $v['prize']['prize_img']);
		    		if(isset($prize_img[0])){
		    			$res[$k]['min_img'] = GetImgBySize($prize_img[0]);
		    		}else{
		    			$res[$k]['min_img'] = '';
		    		}
		    		if(isset($prize_img[1])){
		    			$res[$k]['big_img'] = GetImgBySize($prize_img[1], 'big');
		    		}else{
		    			$res[$k]['big_img'] = '';
		    		}
    				$res[$k]['box_num'] = $v['box_num'];
    				$res[$k]['time'] = date('Y-m-d H:i:s', $v['draw_time']);
    			}
    			// P($res);
    			return ShowRes(0, '', $res);
    		}
    	}
    	return ShowRes(0, '', []);
    }


    /*
    取出当前场次 未中出去的奖品
    */
    public function actionSurplusPrize()
    {
    	header('Access-Control-Allow-Origin:*');
    	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if(!empty($season)){
    		/*取出此场次的奖品*/
    		$ratioList = Ratio::find()->joinWith('prize')->andWhere('{{%ratio}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->orderBy(['id' => SORT_ASC])->asArray()->all();
    		// P($ratioList);
    		if(!empty($ratioList)){
    			$res = [];//返回的数据
    			foreach($ratioList as $k => $v){
    				if($v['total_num'] - $v['out_num']){
	    				$res[$k]['prize_name'] = $v['prize']['prize_name'];
	    				$prize_img = explode(',', $v['prize']['prize_img']);
			    		if(isset($prize_img[0])){
			    			$res[$k]['min_img'] = GetImgBySize($prize_img[0]);
			    		}else{
			    			$res[$k]['min_img'] = '';
			    		}
			    		if(isset($prize_img[1])){
			    			$res[$k]['big_img'] = GetImgBySize($prize_img[1], 'big');
			    		}else{
			    			$res[$k]['big_img'] = '';
			    		}
	    				$res[$k]['surplus'] = $v['total_num'] - $v['out_num'];
    				}
    			}
    			// P($res);

    			/*合并成一个大数组*/
    			$res2 = [];
    			foreach($res as $k => $v){
    				while($res[$k]['surplus'] > 0){
    					array_push($res2, array(
    						'prize_name' => $v['prize_name'],
    						'min_img' => $v['min_img'],
    						'big_img' => $v['big_img']
    					));
    					$res[$k]['surplus']--;
    				}
    			}
    			shuffle($res2);//随机打乱顺序
    			// P($res2);

    			/*全部翻转*/
	            $this->actionWriteRecord(2, 1);


    			return ShowRes(0, '', $res2);
    		}
    	}
    	return ShowRes(0, '', []);
    }


    /*取出最后中出的奖品，主要为了大屏显示*/
    public function actionLastPrize()
    {
    	header('Access-Control-Allow-Origin:*');
    	/*取出当前场次，按照当前时间计算*/
    	$season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->asArray()->one();
    	// P($season);
    	if(!empty($season)){
    		/*取出此场次 最后中出去的奖品*/
    		$drawLog = DrawLog::find()->joinWith('prize')->andWhere('{{%draw_log}}.lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->orderBy(['draw_time' => SORT_DESC])->asArray()->one();
    		// P($drawLog);
    		$res = [];//返回的数据
    		if(!empty($drawLog)){
				$res['prize_name'] = $drawLog['prize']['prize_name'];
				$prize_img = explode(',', $drawLog['prize']['prize_img']);
	    		if(isset($prize_img[0])){
	    			$res['min_img'] = GetImgBySize($prize_img[0]);
	    		}else{
	    			$res['min_img'] = '';
	    		}
	    		if(isset($prize_img[1])){
	    			$res['big_img'] = GetImgBySize($prize_img[1], 'big');
	    		}else{
	    			$res['big_img'] = '';
	    		}
				$res['box_num'] = $drawLog['box_num'];
				$res['time'] = date('Y-m-d H:i:s', $drawLog['draw_time']);
				$res['count'] = DrawLog::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season['sid'])->count();
    			
    			// P($res);
    			return ShowRes(0, '', $res);
    		}
    	}
    	return ShowRes(0, '', []);
    }


    /*
    答题计时，是否弹窗，写入文件
    $flag 	0-答题1-弹窗2-打开剩余奖品
    $v 		0-关闭1-开启
    */
    public function actionWriteRecord($flag = 0, $v = 1)
    {
    	header('Access-Control-Allow-Origin:*');
    	$flag = (int)$flag;
    	$v = (int)$v;
    	if(($flag !== 0 and $flag !== 1 and $flag !== 2) or ($v !== 0 and $v !== 1)){
    		return ShowRes(30030, '参数有误！');
            Yii::$app->end();
    	}
    	
    	$txt = $this->readRecord();//读取文件里的数据

		if(!empty($txt)){
			$data = explode(',', $txt);
		}
		// P($data);
    	$myfile = fopen("info.txt", "w") or die("Unable to open file!");
    	$data[$flag] = $v;
		$txt = implode(',', $data);
		if(fwrite($myfile, $txt)){
			fclose($myfile);
			return ShowRes(0, '操作成功');
		}else{
			fclose($myfile);
			return ShowRes(30000, '操作失败');
		}
    }


    /*
    查看文件里的信息
	$flag 	0-答题1-弹窗2-翻开剩余奖品
    */
    public function actionGetRecord($flag = 0)
    {
    	header('Access-Control-Allow-Origin:*');
    	$flag = (int)$flag;
    	if($flag !== 0 and $flag !== 1 and $flag !== 2){
    		return ShowRes(30030, '参数有误！');
            Yii::$app->end();
    	}
    	
    	$txt = $this->readRecord();//读取文件里的数据

		if(!empty($txt)){
			$data = explode(',', $txt);
			// P($data);
			return ShowRes(0, '', $data[$flag]);
		}
    }

    /*
    抽奖
    $boxNum 	盒子编号，比如选中的几个金蛋
    */
    public function actionLuckyDraw()
    {
    	header('Access-Control-Allow-Origin:*');
    	$post = Yii::$app->request->post();
    	// $post = array(
    	// 	'boxNum' => 1
    	// );
        $boxNum = isset($post['boxNum'])?(int)$post['boxNum']:'';
        if($boxNum === ''){
            return ShowRes(30030, '参数有误！');
            Yii::$app->end();
        }

        /*验证此场次，此盒子有没有被抽取，如果已经抽取，将不能再打开*/
        $season = Season::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere(['<', 'luckydraw_begin_time', time()])->andWhere(['>', 'luckydraw_end_time', time()])->one();
        $drawLog = DrawLog::find()->where('lid = :lid', [':lid' => Yii::$app->params['lid']])->andWhere('sid='.$season->sid)->andWhere('box_num='.$boxNum)->one();
        // P($drawLog);
        if(!empty($drawLog)){
        	return ShowRes(31102, '此盒子已经中出！');
        }
    	
        /*取出当前场次的奖品*/
    	$prizeList = $this->currSeasonPrize();
    	// P($prizeList);
    	if(!empty($prizeList)){
    		$prize = $this->luckyDrawByRandom($prizeList);//得出随机抽取的奖品
    		// P($prize['prize']);
    		/*整理中奖信息，存入数据库*/
    		$data = array(
    			'DrawLog' => array(
    				'pid' => $prize['pid'],
    				'sid' => $prize['sid'],
    				'box_num' => $boxNum,
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

	            /*倒计时关闭，弹窗开启*/
	            $this->actionWriteRecord(0, 0);
	            $this->actionWriteRecord(1, 1);

    			/*整理返回的奖品信息*/
    			$res = [];
    			$res['prize_name'] = $prize['prize']['prize_name'];
    			$prize_img = explode(',', $prize['prize']['prize_img']);
	    		if(isset($prize_img[0])){
	    			$res['min_img'] = GetImgBySize($prize_img[0]);
	    		}else{
	    			$res['min_img'] = '';
	    		}
	    		if(isset($prize_img[1])){
	    			$res['big_img'] = GetImgBySize($prize_img[1], 'big');
	    		}else{
	    			$res['big_img'] = '';
	    		}

                return ShowRes(0, '抽奖成功', $res);
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

    	return ShowRes(31101, '全部中完了'); 	
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


    /*读取 答题计时，是否弹窗 文件*/
    private function readRecord()
    {
    	error_reporting(E_ALL^E_NOTICE^E_WARNING);
    	$myfile = fopen("info.txt", "r") or die("Unable to open file");
		$txt = fread($myfile, filesize("info.txt"));
		fclose($myfile);
		return $txt;
    }


}
