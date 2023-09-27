<?php

namespace imessage\controllers\api;

use Yii;
class CodeController
{
    public function actionCreate(){
        $host = 'http://www.fdyscloud.com.cn/tuling/predict';
        $username = '';
        $password = '';
        $request = Yii::$app->request;
        $img = $request->post('img');
        $model_id =  $request->post('model_id','04897896');
        $version =  $request->post('version','3.1.1');
        $data =[
            'username'=> $username,
            'password'=> $password,
            'ID'=> $model_id,
            'b64'=> $img,
            'version'=>'3.1.1'
        ];
        
    }
}