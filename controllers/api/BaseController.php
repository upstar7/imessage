<?php

namespace imessage\controllers\api;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 *
 * @package imessage\controllers\api
 */
class BaseController extends Controller
{

    /**
     * @param $data
     * @return false|string
     */
    public function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data);
    }

    /**
     * @param $data
     * @return string
     */
    public function html($data)
    {
        header('Content-Type: text/html; charset=utf-8');
        return (string)$data;
    }

    /**
     * @param $message
     * @param $data
     * @param $header
     * @return false|string
     */
    public function error($message='Error', $data = [], $header = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode([
            'code' => 0,
            'message' => $message,
            'time' => time(),
            'time_text' => date('Y-m-d h:i:s'),
            'data' => $data
        ]);
    }

    /**
     * @param $message
     * @param $data
     * @param $header
     * @return false|string
     */
    public function success($message="Success", $data = [], $header = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode([
            'code' => 1,
            'message' => $message,
            'time' => time(),
            'time_text' => date('Y-m-d h:i:s'),
            'data' => $data,
        ]);
    }

    /**
     * 认证
     * @return bool|void
     */
    public function auth()
    {
        $require = Yii::$app->request;
        $tokens = Yii::$app->cache->get('iMessage_token', []);
        if (empty($tokens) or empty(array_keys($tokens))) {
            return false;
        }
        $header = $require->getHeaders();
        if (isset($header['token']) and in_array($header['token'], array_keys($tokens))) {
            $tmp =$tokens[$header['token']];
            $tmp['get_number'] +=1;
            $tmp['time']= time();
            Yii::$app->cache->set('iMessage_token',
                ArrayHelper::merge($tokens, [$header['token'] => $tmp])
            );
            if ($tokens[$header['token']]['disable']) {
                return false;
            }
            return true;
        }
    }
}