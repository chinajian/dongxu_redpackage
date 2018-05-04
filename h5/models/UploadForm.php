<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    public $file;

    
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}
?>