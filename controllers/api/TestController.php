<?php

namespace imessage\controllers\api;

use Yii;
use yii\helpers\ArrayHelper;

class TestController extends BaseController
{

    public function actionIndex(){
        return $this->success('Success');
    }


}