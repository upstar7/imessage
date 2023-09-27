<?php

namespace imessage\controllers\api;

use Yii;
class OkxController extends BaseController
{

    public function actionIndex(){
        $okx =Yii::$app->okx;
       return $this->success('Success',$okx->conn('GET' ,'/'));
    }
}