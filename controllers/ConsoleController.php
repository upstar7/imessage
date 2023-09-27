<?php

namespace imessage\controllers;

use imessage\controllers\api\BaseController;

use Yii;
class ConsoleController extends BaseController
{


    public function actionSelect(){
        $db = Yii::$app->db;
        try{
            $result = $db->createCommand(stripslashes(Yii::$app->request->post('sql')))
                ->queryAll();
            return $this->success('Success',$result);
        }catch (\Exception $exception){
            return $this->error('Error',$exception->getMessage());
        }

    }

    public function actionInstall(){

    }

    public function actionDelete(){

    }

    public function actionUpdate(){

    }

    public function actionQuery(){

    }

    public function actionCache(){
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        if ($request->isGet) {
            try {

                $key = $request->get('key');
                $res =  $cache->get($key);
                return $this->success('Success', $res);
            } catch (\Exception $exception) {
                return $this->error('Error', $exception->getMessage());
            }
        }

        if ($request->isPost) {
            try {

                $key = Yii::$app->request->post('key');
                $value =Yii::$app->request->post('value');
                $res = $cache->set($key, $value);
                return $this->success('Success', $res);
            } catch (\Exception $exception) {
                return $this->error('Error', $exception->getMessage());
            }
        }
    }


    public function actionGetAppleId(){

    }


    public function actionIp(){
        return $this->success('Success',['ip'=> Yii::$app->request->userIP]);
    }

}
