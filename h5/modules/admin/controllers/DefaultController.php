<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\admin\controllers\BasicController;


class DefaultController extends BasicController
{

    /*控制台*/
    public function actionConsole()
    {
        // $this->layout = 'default';
        return $this->render('console');
    }

}
