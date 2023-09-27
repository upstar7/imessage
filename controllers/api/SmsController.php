<?php

namespace imessage\controllers\api;

use imessage\models\PhoneSms;

use yii\web\Response;
class SmsController extends BaseController
{


    public function actionIndex(){
        if($this->auth()){
            $request = \Yii::$app->request;
            $id = $request->get('id');
            /** @var PhoneSms $sms */
            $sms = PhoneSms::find()->where(['id'=>$id])->asArray;
            if($sms){
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 3,
                    ]
                ]);
                try {
                    return $this->html( file_get_contents($sms->phone_url, false, $context));
                }catch (\Exception $exception){
                    $this->html($exception->getMessage());
                }

            }
            return $this->html('');
        }else{
            return $this->html('你没有访问权限');
        }


    }

    public function actionCreate(){

    }
    public function actionView($id){
        return  $this->success('Success', PhoneSms::findOne($id)->toArray());
    }
}