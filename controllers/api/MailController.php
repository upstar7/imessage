<?php

namespace imessage\controllers\api;

use imessage\models\EMail;
use yii\web\Controller;
use Yii;
class MailController extends BaseController
{

    public function actionIndex(){
        return $this->success('message',EMail::find()->asArray()->one());
    }


    /**
     * 新增
     * @return false|string
     */
    public function actionCreate(){
        try {
            if($this->auth()){
                $request = \Yii::$app->request;
                if($request->isPost){
                    $model =new EMail();
                    $model->email = $request->post("email");
                    $model->email_password = stripslashes($request->post("email_password"));
                    $model->email_host =  $request->post('email_host','mail.dwfhjf.com');
                    $model->email_port =  $request->post('email_port',993);
                    $model->email_url = getRequireUrl(1)."/wp-json/imessage/api/imap?token=".generateUuid();

                    if($model->save()){
                        return $this->success();
                    }else{
                        return $this->error('Error',$model->getErrors());
                    }
                }
            }else{
                return  $this->error('Token 无效');
            }
            return $this->error();
        }catch (\Exception $exception){
            return  $this->error('Error',$exception->getMessage());
        }

    }


    public function actionUpdate($id){
        $model = EMail::findOne($id);
        if (!$model) {
            return $this->error('Error',  'Phone not found.');
        }
        $model->load(\Yii::$app->request->post(), '');
        if ($model->save()) {
            return $this->success('Success',Yii::$app->request->params);
        } else {
            return $this->error('Error',$model->getErrors());
        }
    }
}