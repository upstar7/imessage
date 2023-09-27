<?php

namespace imessage\controllers\api;


use Yii;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use imessage\models\iMessage;

class IndexController extends BaseController
{
    public $tokens;
    public $pattern = 'auto';
    public $request_token;

    public function actionIndex()
    {
        try {

            $require = Yii::$app->request;
            if (!$this->auth()) {
                return $this->error('Token不正确货过期或被禁用');
            }

            $tmp = ($this->tokens)[$this->request_token];

            $tmp['time'] = time();
            if (isset($tmp['route']) and !empty($tmp['route']) and (strpos($tmp['route'], "/") !== false)) {
                $n = (int)explode("/", $tmp['route'])[0];
                $route_time = (int)explode("/", $tmp['route'])[1];
                if (time() > ($tmp['resetting_time'] + $route_time * 60)) {
                    $tmp['resetting_time'] = time();
                    $tmp['resetting_number'] = 0;
                } else {
                    if ($n > $tmp['resetting_number']) {
                        $tmp['resetting_number'] += 1;
                    } else {
                        Yii::$app->cache->set('iMessage_token',
                            ArrayHelper::merge($this->tokens, [$this->request_token => $tmp])
                        );
                        return $this->error('请在' . $route_time . "分钟后再尝试");
                    }
                }
            }

            /** @var iMessage $model */
            $pageSize = $require->get('pageSize', '');
            $type = $this->pattern;

            if ($pageSize == '') {

                $result =$this->getOne($tmp,$type);
            } else {
                $result =$this->getAll($pageSize,$tmp,$type);
            }
            //return $this->error($type,$result);
            Yii::$app->cache->set('iMessage_token',
                ArrayHelper::merge($this->tokens, [$this->request_token => $tmp])
            );
            if(count($result) >0){
                return  $this->success('Success', $result);
            }else{
                return  $this->error('Error');
            }
        } catch (\Exception $exception) {
            return $this->error('Error', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
                "file" => $exception->getFile()
            ]);
        }
    }

    public function actionCreate()
    {
        $require = Yii::$app->request;
        if (!$this->auth()) {
            return $this->error('Token不正确货过期或被禁用');
        }
        $tmp = ($this->tokens)[$this->request_token];
        $count = $tmp['success_number'];
        $tmp['time'] = time();
        $str = stripslashes($require->post('id'));
        preg_match_all("/\d+/", $str, $matches);
        if (isset($matches[0]) and !empty($matches[0])) {
            $ids = $matches[0];
            foreach ($ids as $id) {
                if ($this->pattern == 'auto') {
                    $model = iMessage::findOne($id);
                } else {
                    $model = iMessage::find()->where(['id' => $id, "token" => $this->request_token])->one();
                }
                if ($model) {
                    $count++;
                    $model->status = 1;
                    $model->save();
                    $this->number();
                }
            }
            $tmp['success_number'] = $count;
            Yii::$app->cache->set('iMessage_token',
                ArrayHelper::merge($this->tokens, [$this->request_token => $tmp])
            );

            return $this->success('Success', $ids);
        }
        return $this->error('Error');


    }

    public function number()
    {
        $timeUnix = strtotime(date('Y-m-d', time()));
        if (!Yii::$app->cache->get($timeUnix)) {
            Yii::$app->cache->set($timeUnix, 1);
        } else {
            $number = Yii::$app->cache->get($timeUnix);
            Yii::$app->cache->set($timeUnix, $number + 1);
        }
    }

    public function auth()
    {
        $require = Yii::$app->request;
        $tokens = Yii::$app->cache->get('iMessage_token', []);
        $this->pattern = Yii::$app->cache->get('iMessage_pattern') ?: 'auto';
        if (empty($tokens) or empty(array_keys($tokens))) {
            return false;
        }
        $header = $require->getHeaders();
        if (isset($header['token']) and in_array($header['token'], array_keys($tokens))) {
            if ($tokens[$header['token']]['disable']) {
                return false;
            }
            $this->tokens = $tokens;
            $this->request_token = $header['token'];
            return true;
        }
    }

    /**
     * @param $tmp
     * @param $type
     * @return array|void
     */
    public function getOne(&$tmp,$type){
        if ($type == 'auto') {
            /** @var iMessage $model */
            $model = iMessage::find()
                ->where(['<>', 'status', 1])
                ->orderBy(['get_number' => SORT_ASC, 'id' => SORT_ASC])
                ->one();
        }  elseif($type == 'high') {
            $data = Yii::$app->cache->get("iMessage_data") ?: [];
            if(count($data) >0){
                $result = array_slice($data, 0, 1);
                $r = array_slice($data, 1);
                Yii::$app->cache->set("iMessage_data", $r);
                return $result;
            }else{
                return [];
            }
        }else{
            /** @var iMessage $model */
            $model = iMessage::find()
                ->where(['<>', 'status', 1])
                ->andWhere(['token' => $this->request_token])
                ->orderBy(['get_number' => SORT_ASC, 'id' => SORT_ASC])
                ->one();
        }

        if ($model) {
            $result = [
                'id' => $model->id,
                'message' => $model->message,
                'phone' => $model->phone,
                'status' => $model->status
            ];
            $model->get_number++;
            $model->status = 1;
            if ($model->save()) {
                $tmp['get_number'] += 1;
            }
            return $result;
        }
    }

    /**
     * @param $pageSize
     * @param $tmp
     * @param $type
     * @return array
     */
    public function getAll($pageSize,&$tmp,$type){
        $pageSize = (int)$pageSize;
        $result = [];
        if ($type == 'auto') {

            for ($i = 1; $i <= $pageSize; $i++) {
                $transaction = iMessage::getDb()->beginTransaction();
                try {
                    /** @var iMessage $model */
                    $model = iMessage::find()
                        ->where(['<>', 'status', 1])
                        ->orderBy(['get_number' => SORT_ASC, 'id' => SORT_ASC])
                        ->one();
                    $result[] = [
                        'id' => $model->id,
                        'message' => $model->message,
                        'phone' => $model->phone,
                        'status' => $model->status
                    ];
                    $model->get_number++;
//                    $model->status = 1;
                    $model->save();
                    $transaction->commit();
                } catch (\Exception $exception) {
                    break;
                }
            }
        } elseif ($type == 'strict') {
            for ($i = 1; $i <= $pageSize; $i++) {
                $transaction = iMessage::getDb()->beginTransaction();
                try {
                    /** @var iMessage $model */
                    $model = iMessage::find()
                        ->where(['<>', 'status', 1])
                        ->andWhere(['token' => $this->request_token])
                        ->orderBy(['get_number' => SORT_ASC, 'id' => SORT_ASC])
                        ->one();
                    $result[] = [
                        'id' => $model->id,
                        'message' => $model->message,
                        'phone' => $model->phone,
                        'status' => $model->status
                    ];
                    $model->get_number++;
//                    $model->status = 1;
                    $model->save();
                    $transaction->commit();
                } catch (\Exception $exception) {
                    break;
                }
            }
        } elseif ($type == 'high') {
            $data = Yii::$app->cache->get("iMessage_data") ?: [];
            if (is_array($data)) {
                $count = count($data);
                if ($count >= $pageSize) {
                    $data = Yii::$app->cache->get("iMessage_data") ?: [];
                    $result = array_slice($data, 0, $pageSize);
                    $r = array_slice($data, $pageSize);
                    Yii::$app->cache->set("iMessage_data", $r);
                } else {
                    $result = $data;
                    Yii::$app->cache->set("iMessage_data", []);
                }
            }
        }

        $tmp['get_number'] = ((int)$tmp['get_number'] + count($result));
        return $result;
    }

}
