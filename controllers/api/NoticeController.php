<?php

namespace imessage\controllers\api;

use Yii;
use yii\helpers\ArrayHelper;

class NoticeController extends BaseController
{


    public function actionCreate(){
        $tokens = Yii::$app->cache->get('iMessage_token',[]);
        $header = Yii::$app->request->getHeaders();
        if(isset($header['token']) and in_array($header['token'], array_keys($tokens)) ){
            $tmp =$tokens[$header['token']];
            $tmp['message'] =Yii::$app->request->post('message');
            Yii::$app->cache->set('iMessage_token',
                ArrayHelper::merge($tokens, [$header['token'] => $tmp])
            );
        }
        return $this->success();
    }
}