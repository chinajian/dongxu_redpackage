<?php
/*
此文件定义一些全局函数，方便调用
*/

/*打印*/
if(!function_exists('P')){
    function P($arr)
    {
        echo "<pre>";
        print_r($arr);
        // var_dump($arr);
        echo "</pre>";
        die;
    }
}

/*ajax 返回值*/
if(!function_exists('ShowRes')){
    function ShowRes($code = 200, $mes = '', $data = '', $url = '')
    {
        $res = array(
            "code" => $code,
            "mes" => $mes,
            "data" => $data,
            "url" => $url,
        );
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}


/*产生6位随机字符串*/
if(!function_exists('GenerateStr')){
    function GenerateStr($length=6)
    { 
        // 密码字符集，可任意添加你需要的字符 
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
        $randStr = ''; 
        for($i=0; $i<$length; $i++) { 
            // 这里提供两种字符获取方式 
            // 第一种是使用 substr 截取$chars中的任意一位字符； 
            // $randStr .= substr($chars, mt_rand(0, strlen($chars) – 1), 1); 
            // 第二种是取字符数组 $chars 的任意元素 
            $randStr .= $chars[mt_rand(0, strlen($chars)-1)];
        };
      return $randStr; 
    }
}

/*
根据尺寸，返回对应的图片
如果没有对应的尺寸图，就返回原始图，如果原始图
$Imgurl     需要转换的图片路径
$size       需要转换的图片尺寸
*/
if(!function_exists('GetImgBySize')){
  function GetImgBySize($Imgurl, $size='thumb'){
      $size = $size.'_img';
      $sizes = Yii::$app->params['imgSize'];//所有配置尺寸
      $suffix = explode('.', $Imgurl)[count(explode('.', $Imgurl))-1];
      $tmp = explode('!!', $Imgurl);
      if(count($tmp)>1){//有裁剪尺寸
          $tmp2 = explode('_', $tmp[1]);
          $len = $tmp2[0];//此图片有多少尺寸
          $i = count($sizes);
          foreach(array_reverse($sizes) as $k => $v){
              if($k == $size){
                  if($i <= $len){
                      $newImgurl = $tmp[0].'!!'.$len.'_'.$sizes[$size].'x'.$sizes[$size].'.'.$suffix;
                  }
              }
              $i--;
          }
      };
      $Imgurl = $tmp[0].'.'.$suffix;
      return isset($newImgurl)?$newImgurl:$Imgurl;
  }
}

/*
 * 函数说明：截取指定长度的字符串
 *         utf-8专用 汉字和大写字母长度算1，其它字符长度算0.5
 *
 * @param  string  $str  原字符串
 * @param  int     $len  截取长度
 * @param  string  $etc  省略字符...
 * @return string        截取后的字符串
 */
if(!function_exists('ReStrLen')){
    function ReStrLen($str, $len=10, $etc='...')
    {
        $restr = '';
        $i = 0;
        $n = 0.0;

        //字符串的字节数
        $strlen = strlen($str);
        while(($n < $len) and ($i < $strlen))
        {
           $temp_str = substr($str, $i, 1);

           //得到字符串中第$i位字符的ASCII码
           $ascnum = ord($temp_str);

           //如果ASCII位高与252
           if($ascnum >= 252)
           {
                //根据UTF-8编码规范，将6个连续的字符计为单个字符
                $restr = $restr.substr($str, $i, 6);
                //实际Byte计为6
                $i = $i + 6;
                //字串长度计1
                $n++;
           }
           else if($ascnum >= 248)
           {
                $restr = $restr.substr($str, $i, 5);
                $i = $i + 5;
                $n++;
           }
           else if($ascnum >= 240)
           {
                $restr = $restr.substr($str, $i, 4);
                $i = $i + 4;
                $n++;
           }
           else if($ascnum >= 224)
           {
                $restr = $restr.substr($str, $i, 3);
                $i = $i + 3 ;
                $n++;
           }
           else if ($ascnum >= 192)
           {
                $restr = $restr.substr($str, $i, 2);
                $i = $i + 2;
                $n++;
           }

           //如果是大写字母 I除外
           else if($ascnum>=65 and $ascnum<=90 and $ascnum!=73)
           {
                $restr = $restr.substr($str, $i, 1);
                //实际的Byte数仍计1个
                $i = $i + 1;
                //但考虑整体美观，大写字母计成一个高位字符
                $n++;
           }

           //%,&,@,m,w 字符按1个字符宽
           else if(!(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE))
           {
                $restr = $restr.substr($str, $i, 1);
                //实际的Byte数仍计1个
                $i = $i + 1;
                //但考虑整体美观，这些字条计成一个高位字符
                $n++;
           }

           //其他情况下，包括小写字母和半角标点符号
           else
           {
                $restr = $restr.substr($str, $i, 1);
                //实际的Byte数计1个
                $i = $i + 1;
                //其余的小写字母和半角标点等与半个高位字符宽
                $n = $n + 0.5;
           }
        }

        //超过长度时在尾处加上省略号
        if($i < $strlen)
        {
           $restr = $restr.$etc;
        }

        return $restr;
    }
}

?>