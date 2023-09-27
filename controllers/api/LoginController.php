<?php

namespace imessage\controllers\api;

use Yii;
class LoginController extends BaseController
{


    public function actionIndex(){
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
            ],
        ]);
        $type = $request->get('type');
        // 转发
        if($type=="forward"){
            $code = $request->get("code");
            $token = $request->getHeaders()['token'];
            if($token =='ae5a8ed6-437a-11ee-8d22-00163e006044'){
                if(wp_mail(get_option('admin_email'),"登陆验证码", $code,[],[],$context)){
                    return $this->success('Success');
                }else{
                    return $this->error('你没有开启smtp功能,或超时');
                }
            }
        }

        $code =$cache->get('imessage_mail_code');
        if($code){
            return $this->error('你操作太频繁,稍后再试试');
        }else{
            $code =rand(1000, 9999);
            if(wp_mail(get_option('admin_email'),"登陆验证码", $code,[],[],$context)){
                $cache ->set('imessage_mail_code',$code,60);
                return $this->success('Success');
            }else{
                return $this->error('你没有开启smtp功能,或超时');
            }
        }

    }

    public function actionCreate(){
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $cache_code = (int) $cache->get('imessage_mail_code');
        $post_code = (int) $request->post('code','');
        if(  $cache_code==$post_code){
            $user = get_user_by('id', 1);
            wp_set_current_user(1, $user->user_login);
            wp_set_auth_cookie(1,true,"");
            return $this->success('Success',["redirect"=>admin_url()]);
        }
        return $this->error('Error');
    }
}