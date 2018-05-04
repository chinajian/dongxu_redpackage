<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\modules\admin\models\SysLog;


class Album extends \yii\db\ActiveRecord
{
	public static function tableName()
    {
        return '{{%album}}';
    }

	public function rules()
    {
        return [
            ['cat_id', 'integer', 'message' => '分类格式不正确'],
            ['album_name', 'required', 'message' => '图片名称不能为空'],
            ['album_name', 'string', 'max' => 30],
            ['album_path', 'required', 'message' => '图片路径不能为空'],
            [['is_cover', 'sort', 'add_time'], 'safe'],
        ];
    }


    /*上传图片*/
    public function upload($post, $size = 'thumb_img')
    {
        /*上传过来的图片，放到指定的位置*/
        $UploadFormModel = new UploadForm();
        $UploadFormModel->file = UploadedFile::getInstance($UploadFormModel, 'file');
        // P($UploadFormModel->file);
        if ($UploadFormModel->file && $UploadFormModel->validate()) {
            // $fileName = $UploadFormModel->file->baseName . '.' . $UploadFormModel->file->extension;
            $fileName = date("Ymd").generateStr(3).date("His").generateStr(3);//文件名称
            $fileSuffix = '.' . $UploadFormModel->file->extension;//文件后缀
            $folderName = date('Ymd');//放进今天日期的文件夹中
            $path = Yii::$app->basePath . '/web/' . Yii::$app->params['uploadPath'].$folderName;//存放的路径（不包含文件名）
            if(!file_exists($path)){//创建今天日期的文件夹
                mkdir($path, 0777, true);
                chmod($path, 0777);
            }
            if($UploadFormModel->file->saveAs($path . '/' . $fileName . $fileSuffix)){

                /*插入到相册表中albumId*/
                $data = array(
                    'Album' => array(
                        'cat_id' => $post['catId'],
                        'album_name' => mb_substr($post['name'], 0, 30, 'utf-8'),//截取前30个字符
                        'album_path' => Yii::$app->params['uploadPath'] . $folderName . '/' . $fileName . $fileSuffix,
                        'add_time' => time()
                    )
                );
                if($this->load($data) and $this->validate()){
                    /*保存到相册*/
                    if($this->save(false)){
                        /*写入日志*/
                        SysLog::addLog('上传[' . Yii::$app->params['uploadPath'] . $fileName . $fileSuffix . ']');

                        /*复制 并 处理 其他几种尺寸的图片>>>>>>*/
                        $size = Yii::$app->params['imgSize'];//得到图片的几种尺寸
                        $imgInfo = getimagesize($path . '/' . $fileName . $fileSuffix);//获取图片信息
                        $imgWidth = $imgInfo[0];//图片的宽度
                        $imgHeight = $imgInfo[1];//图片的高度
                        $num = 0;//记录裁剪出了几张图片 默认0张
                        foreach($size as $k => $v){
                            if($imgWidth > $v or $imgHeight > $v){
                                $num++;
                            }
                        }
                        foreach($size as $k => $v){
                            if($imgWidth > $v or $imgHeight > $v){
                                $this->image_resize($path . '/' . $fileName . $fileSuffix, $path . '/' . $fileName . '!!' . $num . '_' . $v . 'x' . $v . $fileSuffix, $v, $v);
                            }
                        }
                        if($num >= count($size)){//如果每个尺寸都裁剪了，说明此图特别大，需删除原图
                            unlink($path . '/' . $fileName . $fileSuffix);//删除原图
                        }
                        /*复制 并 处理 其他几种尺寸的图片<<<<<<*/

                        if($num >= 1){
                            return SITE_URL . Yii::$app->params['uploadPath'] . $folderName . '/' . $fileName . '!!' . $num . '_' . $size['mini_img'] . 'x' . $size['mini_img'] . $fileSuffix;
                        }
                        return SITE_URL . Yii::$app->params['uploadPath'] . $folderName . '/' . $fileName . $fileSuffix;
                    }
            	}
                
            }

            return false;

        }

    }


    /*
    把大图缩略到缩略图指定的范围内,可能有留白（原图细节不丢失，不失真）
    $f      原始图片（包含路径）
    $t      裁剪之后的图片（包含路径）
    $tw     图片的宽度
    $th     图片的高度
    */
    function image_resize($f, $t, $tw, $th){
    // 按指定大小生成缩略图，而且不变形，缩略图函数
        $temp = array(1=>'gif', 2=>'jpeg', 3=>'png');

        list($fw, $fh, $tmp) = getimagesize($f);

        if(!$temp[$tmp]){
            return false;
        }
        $tmp = $temp[$tmp];
        $infunc = "imagecreatefrom$tmp";
        $outfunc = "image$tmp";

        $fimg = $infunc($f);

        // 使缩略后的图片不变形，并且限制在 缩略图宽高范围内
        if($fw/$tw > $fh/$th){
            $th = $tw*($fh/$fw);
        }else{
            $tw = $th*($fw/$fh);
        }

        $timg = imagecreatetruecolor($tw, $th);
        imagealphablending($timg, true);
        imagesavealpha($timg, true);
        $trans_colour = imagecolorallocatealpha($timg, 0, 0, 0, 127);
        imagefill($timg, 0, 0, $trans_colour);
        imagecopyresampled($timg, $fimg, 0,0, 0,0, $tw,$th, $fw,$fh);
        if($outfunc($timg, $t)){
            return true;
        }else{
            return false;
        }
    }

    /*失真缩放*/
    function img_create_small($big_img, $width, $height, $small_img)
    {//原始大图地址，缩略图宽度，高度，缩略图地址
        $imgage = getimagesize($big_img); //得到原始大图片
        switch ($imgage[2]) { // 图像类型判断
            case 1:
                $im = imagecreatefromgif($big_img);
                break;
            case 2:
                $im = imagecreatefromjpeg($big_img);
                break;
            case 3:
                $im = imagecreatefrompng($big_img);
                break;
        }
        $src_W = $imgage[0]; //获取大图片宽度
        $src_H = $imgage[1]; //获取大图片高度
        $tn = imagecreatetruecolor($width, $height); //创建缩略图
        imagecopyresampled($tn, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); //复制图像并改变大小
        imagejpeg($tn, $small_img); //输出图像
    }

}
?>