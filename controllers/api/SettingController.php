<?php

namespace imessage\controllers\api;

use imessage\models\AppleId;
use imessage\models\iMessage;
use Yii;
class SettingController extends BaseController
{

    public function actionIndex(){
        if($this->auth()){
            $request= Yii::$app->request;
            $id = $request->get('id',"");
            $model=false;
            if($id !==''){
                $model = AppleId::find()->where(['id'=>$id])->one();
            }else{
                $model = AppleId::find()
                    ->where(['<>', 'status', 0])
                    ->orderBy(['get_number' => SORT_ASC,'id' => SORT_ASC])
                    ->one();
            }
            if($model){
              /** @var $model AppleId */
                $model->get_number  = $model->get_number +1;
                if($model->save()){
                    return  $this->success('Success',AppleId::find()->where(['id'=>$model->id])->asArray()->one());
                }else{
                    return  $this->error('Error',$model->getErrors());
                }

            }
        }

       return $this->error('Error');
    }

    public function actionCreate(){
        if($this->auth()){
            $cache =Yii::$app->cache;
            $message = $cache->get('iMessage_message')?:"";
           if($message==""){
               $model =iMessage::find()
                   ->orderBy(['id' => SORT_ASC]) // 按照 ID 升序排序
                   ->asArray()
                   ->one();
           }else{
               $model =[
                   'message'=>$message
               ];
           }

            return $this->success('Success',$model);
        }
        return $this->error('Error');
    }
}
