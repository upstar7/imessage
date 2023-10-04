<?php

namespace imessage\controllers;

use imessage\models\Task;
use Yii;
use \Exception;
use imessage\models\AppleId;
use imessage\models\EMail;
use imessage\models\PhoneSms;
use yii\helpers\ArrayHelper;
use imessage\models\iMessage;
use yii\web\Controller;

/**
 * Class IndexController
 * @package imessage\controllers
 */
class IndexController extends Controller
{
    public $enableCsrfValidation = false;

    private $_modelClass;
    public $layout = false;

    private function menu(){
        return json_encode( [
                ['url'=>'imessage','text'=>'iMessage'],
                ['url'=>'index/message','text'=>'消息'],
                ['url'=>'index/saolan','text'=>'扫蓝'],
                ['url'=>'index/token','text'=>'令牌'],
                ['url'=>'index/apple','text'=>'Apple ID'],
                ['url'=>'index/mail','text'=>'邮箱'],
                ['url'=>'index/phone','text'=>'接码'],
                ['url'=>'index/tutorial','text'=>'教程'],
                ['url'=>'index/rules','text'=>'路由'],
                ['url'=>'index/book','text'=>'记事本'],
                ['url'=>'index/task','text'=>'任务'],
                ['url'=>'index/setting','text'=>'设置'],

            ]);
    }

    public function actions()
    {
        return [
            'index',
            'modules',
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(){
        if(Yii::$app->request->isAjax){
            return  $this->iMessage();
        }
        $cache =Yii::$app->cache;
        $type = $cache->get('iMessage_pattern') ?: 'auto';
        $timeUnix = strtotime(date('Y-m-d',time()));
        $total = iMessage::find()->count() ?: 0;

        if($type=='high'){
            $success = $total - ($cache->get('iMessage_data',[])? count($cache->get('iMessage_data',[])) :0);
            $pie =json_encode( [
                [ 'value'=> $success, 'name'=> '已发送' ],
                [ 'value'=> $total- $success, 'name'=> '待发送' ],
            ]);
        }else{
            $success = count(iMessage::find()->where(['status'=>1])->all()) ?: 0;
            $pie =json_encode( [
                [ 'value'=> $success, 'name'=> '已发送' ],
                [ 'value'=> $total- $success, 'name'=> '待发送' ],
            ]);
        }

        try{
            $baifenbi =(int)(($success/$total)*100) ."%";
            $n =  (int)(($success/$total)*5) ?:0;
        }catch (Exception $exception){
            $baifenbi ="0%";
            $n =0;
        }
        $x =$y =[];
        for($i=7;$i>=0;$i--){
            $tmp =$timeUnix - 24*60*60 *$i;
            $value =0;
            if(Yii::$app->cache->get($tmp)){
                $value=Yii::$app->cache->get($tmp) ;
            }
            $x[]= date('d',$tmp).'号';
            $y[]=$value;
        }
        $ips =[];
        $file = Yii::$app-> basePath;
        if(file_exists($file."/a.txt")){
            $str =file_get_contents($file."/a.txt");
            $pattern = '/\b(?:\d{1,3}\.){3}\d{1,3}\b/';
            if(preg_match_all($pattern, $str , $matches) and isset($matches[0])){
                $ips =$matches[0];
            }
        }
        return $this->render("index",[
            'sum'=>array_sum($y),
            "x"=>json_encode($x),
            "y"=> json_encode($y),
            'pie'=>$pie,
            'total'=>$total,
            'success'=>$success,
            'baifenbi'=>$baifenbi,
            'submenu'=>$this->menu(),
            'n'=>$n,
            'ips'=>json_encode($ips)
        ]);
    }

    /**
     * 教程
     * @return string
     */
    public function actionTutorial(){
        return $this->render('tutorial',[
            'submenu'=>$this->menu(),
        ]);
    }

    public function actionSaolan(){

        if(Yii::$app->request->isAjax){
            $this->_modelClass = iMessage::class;
            return $this->ajaxApi();
        }

        $columns ="[
                {'field':'id' ,'label': 'ID','zh_CN':'ID','style':'width:40px'},
                {'field':'phone' ,'label': 'Phone','zh_CN':'手机号'},
                {'field':'get_number' ,'label': 'Get Number','zh_CN':'获取次数'},
                {'field':'status' ,'label': 'Status','zh_CN':'状态'},
                {'field':'created_at' ,'label': 'Created At','style':'width: 150px;','zh_CN':'创建时间'},
                {'field':'updated_at' ,'label': 'Updated At','style':'width: 150px','zh_CN':'修改时间'}
            ]";
        return $this->render('apple',[
            'modelName'=>"iMessage",
            'activeUrl'=>'index/saolan',
            'columns'=>$columns,
            'submenu'=>$this->menu(),
            'installDefaultFields'=>"[{'key':'phone','value':'+1 (美国)'}]"
        ]);
    }
    public function actionPhone(){
        if(Yii::$app->request->isAjax){
            $this->_modelClass = PhoneSms::class;
            return $this->ajaxApi();
        }

        $columns ="[
                {'field':'id' ,'label': 'ID','zh_CN':'ID','style':'width:40px'},
                {'field':'phone' ,'label': 'Phone','zh_CN':'手机号'},
                {'field':'phone_country' ,'label': 'Phone Country','zh_CN':'区号'},
                {'field':'phone_url' ,'label':'Phone Url','zh_CN':'接码'},
                {'field':'get_number' ,'label': 'Get Number','zh_CN':'获取次数'},
                {'field':'success_number' ,'label': 'Get Number','zh_CN':'成功次数'},
                {'field':'status' ,'label': 'Status','zh_CN':'状态'},
                {'field':'created_at' ,'label': 'Created At','style':'width: 150px;','zh_CN':'创建时间'},
                {'field':'updated_at' ,'label': 'Updated At','style':'width: 150px','zh_CN':'修改时间'}
            ]";
        return $this->render('apple',[
            'modelName'=>"PhoneSms",
            'activeUrl'=>'index/phone',
            'columns'=>$columns,
            'submenu'=>$this->menu(),
            'mysqlTableName'=>PhoneSms::tableName(),
            'installDefaultFields'=>"[{'key':'phone_country','value':'+1 (美国)'}]"
        ]);
    }

    public function actionMail(){
        if(Yii::$app->request->isAjax){
            $this->_modelClass = EMail::class;
            return $this->ajaxApi();
        }
        $columns ="[
                {'field':'id' ,'label': 'ID','zh_CN':'ID','style':'width:40px'},
                {'field':'email' ,'label': 'Emali','zh_CN':'邮箱'},
                {'field':'email_password' ,'label': 'Emali Password','zh_CN':'邮箱密码'},
                {'field':'email_url' ,'label': 'Emali Url','zh_CN':'邮件接码'},
                {'field':'email_host'  ,'label': 'Emali Host','zh_CN':'服务器'},
                {'field':'email_port' ,'label': 'Emali Port','zh_CN':'端口号'},
                {'field':'get_number' ,'label': 'Get Number','zh_CN':'获取次数'},
                {'field':'status' ,'label': 'Status','zh_CN':'状态'},
                {'field':'created_at' ,'label': 'Created At','style':'width: 150px;','zh_CN':'创建时间'},
                {'field':'updated_at' ,'label': 'Updated At','style':'width: 150px','zh_CN':'修改时间'}
            ]";
        return $this->render('apple',[
            'modelName'=>"EMail",
            'activeUrl'=>'index/mail',
            'columns'=>$columns,
            'submenu'=>$this->menu(),
            'mysqlTableName'=>EMail::tableName(),
            'installDefaultFields'=>"[{'key':'email_host','value':'mail.dwfhjf.com'},{'key':'email_port','value':'993'}]"
        ]);
    }

    public function actionApple(){
        if(Yii::$app->request->isAjax){
            $this->_modelClass= AppleId::class;
            return $this->ajaxApi();
        }
        $columns ="[
                {'field':'id' ,'label': 'ID','zh_CN':'ID','style':'width:40px'},
                {'field':'apple_id','label': 'Apple ID','zh_CN':'Apple ID'},
                {'field':'apple_password' ,'label': 'Apple Password','zh_CN':'密码'},
                {'field':'first_name' ,'label': 'First Name','zh_CN':'姓'},
                {'field':'last_name','label':'Last Name','zh_CN':'名',},
                {'field':'date_of_birth' ,'label': 'Date Of Birth','zh_CN':'出生日期'},
                {'field':'country' ,'label': 'Country','zh_CN':'地区'},
                {'field':'phone' ,'label': 'Phone','zh_CN':'手机号'},
                {'field':'phone_country' ,'label': 'Phone Country','zh_CN':'区号'},
                {'field':'phone_url' ,'label':'Phone Url','zh_CN':'接码'},
                {'field':'email' ,'label': 'Emali','zh_CN':'邮箱'},
                {'field':'email_password' ,'label': 'Emali Password','zh_CN':'邮箱密码'},
                {'field':'email_url' ,'label': 'Emali Url','zh_CN':'邮件接码'},
                {'field':'get_number' ,'label': 'Get Number','zh_CN':'获取次数'},
                {'field':'status' ,'label': 'Status','zh_CN':'状态'},
                {'field':'notes' ,'label': 'Notes','zh_CN':'备注'},
                {'field':'created_at' ,'label': 'Created At','style':'width: 150px;','zh_CN':'创建时间'},
                {'field':'updated_at' ,'label': 'Updated At','style':'width: 150px','zh_CN':'修改时间'}
            ]";
        return $this->render('apple',[
            'modelName'=>"AppleId",
            'activeUrl'=>'index/apple',
            'columns'=>$columns,
            'submenu'=>$this->menu(),
            'mysqlTableName'=>AppleId::tableName(),
            'installDefaultFields'=>"[{'key':'','value':''}]"
        ]);
    }


    /**
     * token
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionToken(){
        $request= Yii::$app->request;
        if($request->isAjax){
            $cache = Yii::$app->cache;
            if($request->isGet){
                $type = $request->get('type','');
                if(empty($type) or $type =='get'){
                    $tokens = Yii::$app->cache->get('iMessage_token',[]);
                    return  $this->success('Success', $tokens);
                }else if ($type =='flush'){
                    $cache->flush();
                    return  $this->success();
                }else if ($type =='Refresh'){
                    $tokens = Yii::$app->cache->get('iMessage_token',[]);
                    $tmp = [];
                    foreach ($tokens as $token =>$arr){
                        $arr[ 'success_number']=0;
                        $arr[ 'get_number']=0;
                        $arr[ 'message']=0;
                        $tmp[$token] =$arr;
                    }
                    $cache->set('iMessage_token',$tmp);
                    return  $this->success('Success', $tmp);
                }
            }elseif ($this->request->isPost){
                $type = $request->post('type','');
                $tokens = Yii::$app->cache->get('iMessage_token',[]);
                if($type=='create'){
                    $token =Yii::$app->db->createCommand("select uuid() as uuid")->queryOne()['uuid'];
                    $data =[
                        $token=>[
                            'name'=>$request->post('name'),
                            'success_number'=>0,
                            'get_number'=>0,
                            //重置时间
                            'resetting_time'=>time(),
                            'disable'=>0,
                            'resetting_number'=>0,
                            'message'=>'',
                            'route'=>$request->post('route'),
                            'time'=>time(),
                        ]
                    ] ;
                    $tokens = ArrayHelper::merge($tokens,$data);
                    $cache->set('iMessage_token',$tokens);
                    return $this->success('Success',$tokens);
                }elseif ($type=='disableChange'){
                    $token = $request->post('token');
                    foreach ($tokens as $key =>$value){
                        if($token == $key){
                            $tokens[$key]['disable'] = (!isset($tokens[$key]['disable']))?0:!$tokens[$key]['disable'];
                            $cache->set('iMessage_token',$tokens);
                            return $this->success();
                        }
                    }
                    return  $this->error();
                }elseif ($type=='delete'){
                    $ids = $request->post('ids',[]);
                    foreach ($ids as $id){
                        unset($tokens[$id]);
                    }
                    $cache->set('iMessage_token',$tokens);
                    return $this->success('Success',$tokens);
                }
            }
        }
        return $this->render('token',[
            'submenu'=>$this->menu(),
        ]);
    }

    /**
     * 路由
     * @return false|string
     */
    public function actionRules(){
        if( Yii::$app->request->isAjax){
            return $this->success('ok',get_option('rewrite_rules'));
        }
        return $this->render('rules',[
            'activeUrl'=>'index/apple',
            'submenu'=>$this->menu(),
        ]);
    }

    public function actionMessage(){
        return $this->render('message',[
            'activeUrl'=>'index/message',
            'submenu'=>$this->menu(),
        ]);
    }

    public function actionBook(){
        return $this->render('book',[
            'activeUrl'=>'index/book',
            'submenu'=>$this->menu(),
        ]);
    }

    public function actionTask(){
        if(Yii::$app->request->isAjax){
            $this->_modelClass= Task::class;
            return $this->ajaxApi();
        }
        $columns ="[
                {'field':'id' ,'label': 'ID','zh_CN':'ID','style':'width:40px'},
                {'field':'name' ,'label': 'ID','zh_CN':'任务名称'},
                {'field':'number' ,'label': 'ID','zh_CN':'任务数量'},
                {'field':'active_number' ,'label': 'ID','zh_CN':'已完成'},
                {'field':'notice_type' ,'label': 'ID','zh_CN':'通知类型'},
                {'field':'notice_url' ,'label': 'ID','zh_CN':'通知Url'},
                {'field':'notice_token' ,'label': 'ID','zh_CN':'通知Token'},
                {'field':'notice_mail' ,'label': 'ID','zh_CN':'通知邮箱'},
                {'field':'status' ,'label': 'Status','zh_CN':'状态'},
                {'field':'created_at' ,'label': 'Created At','style':'width: 150px;','zh_CN':'创建时间'},
                {'field':'updated_at' ,'label': 'Updated At','style':'width: 150px','zh_CN':'修改时间'}
            ]";
        return $this->render('apple',[
            'modelName'=>"Task",
            'activeUrl'=>'index/task',
            'columns'=>$columns,
            'submenu'=>$this->menu(),
            'mysqlTableName'=> Task::tableName(),
            'installDefaultFields'=>"[{'key':'','value':''}]"
        ]);
    }
    public function actionSetting(){
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('section',[
                'option_group'=>Yii::$app->request->get('option_group'),
                'page'=>'index/setting',
            ]);
        }
        return $this->render('setting',[
            'activeUrl'=>'index/setting',
            'submenu'=>$this->menu(),
        ]);
    }

    /**
     * 下是ajax
     *
     *
     *
     *
     * @return void
     */
    public function ajaxApi(){
        if(Yii::$app->request->isGet){
            return $this->ajaxSelect();
        }
        if(Yii::$app->request->isPost){
            return $this->ajaxCreate();
        }
        if(Yii::$app->request->isDelete){
            return $this->ajaxDelete();
        }
        if(Yii::$app->request->isPatch){
            return $this->ajaxPatch(Yii::$app->request->get('id'));
        }
        if(Yii::$app->request->isPut){
            return $this->ajaxPut();
        }
    }

    /**
     * 查询
     * @return false|string
     */
    public function ajaxSelect(){
        $class = $this->_modelClass;
        $request = Yii::$app->request;
        $query = $class::find();
        if($request->get('where')){
            $query->where($request->get('where'));
        }
        if($request->get('andWhere')){
            $query->where($request->get('andWhere'));
        }
        $page =$request->get('page', 1)-1;
        $pageSize = $request->get('pageSize', 10);
        $total = $query->count();
        //SORT_DESC 降序
        //SORT_ASC 升序
        $orderBy=$request->get('orderBy', 'id desc');
        if($pageSize =="all"){
            $table = $query->orderBy($orderBy)
                ->asArray()
                ->all();
        }else{
            $table = $query->offset($page*$pageSize)
                ->limit($pageSize)
                ->orderBy($orderBy)
                ->asArray()
                ->all();
        }

        return  $this->success('ok', [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'table' => $table ,
        ]);
    }

    /**
     * 创建
     * @return false|string
     */
    public function ajaxCreate(){
        $class = $this->_modelClass;
        $request =Yii::$app->request;
        /** @var yii\db\ActiveRecord $model */
        $model = new $class();
        $post =$request->post();
        $tmp =explode('\\',get_class($model));
        $modelName= end($tmp);
        if($this->isTwoDimensionalArray($post[$modelName])){
            $success =0;
            foreach ($post[$modelName] as $row){
                if($success !=0){
                    $model =new $class();
                }
                if($model->load([$modelName=>$row]) && $model->save(false)){
                    $success ++;
                }else{
                    logObject([
                        'model'=>$row,
                        'error'=>$model->getErrors()
                    ]);
                }
            }
            return $this->success('Success',  ['success'=>$success,'error'=>count($post[$modelName])-$success]);
        }

        if($model->load($request->post()) && $model->save()){
            return $this->success('Success',  $class::findone($model->id)->toArray());
        }
        return  $this->error('Error',$model->getErrors());
    }

    public function  isTwoDimensionalArray($variable) {
        if (!is_array($variable)) {
            return false;
        }
        foreach ($variable as $value) {
            if (!is_array($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除
     * @return false|string
     */
    public function ajaxDelete(){
        /** @var yii\db\ActiveRecord $model */
        $class = $this->_modelClass;
        $request =Yii::$app->request;
        if($class::deleteAll(['id'=>$request->post('ids')])){
            return $this->success();
        }
        return  $this->error();
    }

    /**
     * 修改
     * @param $id
     * @return false|string
     */
    public function ajaxPatch($id){
        $class = $this->_modelClass;
        $request =Yii::$app->request;
        /** @var yii\db\ActiveRecord $model */
        $model =  $class::findone($id);
        try {
            if($model->load($request->post()) && $model->save()){
                return $this->success('Success',  $class::findone($id)->toArray());
            }
            return  $this->error('Error',$model->getErrors());
        }catch (Exception $exception){
            return  $this->error();
        }

    }

    /**
     * 清空
     * @return false|string
     */
    public function ajaxPut(){
        $class = $this->_modelClass;
        /** @var yii\db\ActiveRecord $model */
        try {
            $tableName = $class::tableName();
            $class::deleteAll();
            Yii::$app->db->createCommand("ALTER TABLE $tableName AUTO_INCREMENT = 1")->execute();
            return $this->success();
        }catch (Exception $exception){
            return  $this->error();
        }
    }

    public function error($message='Error', $data = [],$header=[])
    {
        header('Content-Type: application/json');
        return json_encode([
            'code' => 0,
            'message' => $message,
            'time' => time(),
            'data' => $data
        ]);
    }

    public function success($message='Success', $data = [],$header=[])
    {
        header('Content-Type: application/json');
        return json_encode([
            'code' => 1,
            'message' => $message,
            'time' => time(),
            'data' => $data,
        ]);
    }


    public function iMessage(){
        $request =Yii::$app->request;
        if( $request->isGet){
            $type = $request->get('type','');
            if(empty($type)){
                return $this->select();
            }
        }else{
            $type =  $request->post('type','');
            if($type =='add'){
                return $this->create();
            }elseif ($type=='update'){
                return  $this->update();
            }elseif ($type=='delete'){
                return  $this->delete();
            }elseif ($type=='adds'){
                return  $this->adds();
            }elseif ($type=='reset'){
                return  $this->reset();
            } elseif ($type=='settings'){
                return  $this->settings ();
            } elseif ($type=='resetDb'){
                return  $this->resetDb ();
            }elseif ($type=='deleteAllYiFaSong'){
                return  $this->deleteAllYiFaSong ();
            }
        }
    }

    public function select(){
        $request = Yii::$app->request;
        $cache =Yii::$app->cache;
        $type = $cache->get('iMessage_pattern') ?: 'auto';
        $query =iMessage::find() ;
        if($request->get('where')){
            $query->where($request->get('where'));
        }
        if($request->get('andWhere')){
            $query->where($request->get('andWhere'));
        }
        $page =$request->get('page', 1)-1;
        $pageSize = $request->get('pageSize',10);
        $total = $query->count();
        $table=[];
        if($total!==0){
            if($pageSize =='' or $pageSize =='all'){
                $results = $query->all();
            }else{
                $results = $query->offset($page*$pageSize)
                    ->limit($pageSize)
                    ->all();
            }
            if( $type=='high'){
                $data = $cache->get("iMessage_data");
                /** @var iMessage $result */
                if(empty($data)){
                    /** @var iMessage $result */
                    foreach ($results as $result){
                        $table[]=[
                            'id'=>$result->id,
                            'message'=>$result->message,
                            'phone'=>$result->phone,
                            'status'=>1   ,
                            'token'=>$result->token,
                            'get_number'=>$result->get_number,
                            'createdTime'=> $result->createdTime,
                            'updatedTime'=>$result->updatedTime,
                            'created_at'=>$result->created_at,
                            'updated_at'=>$result->updated_at
                        ];
                    }
                } elseif(is_array($data) and !empty($data)){
                    $phones =array_unique(array_column($data, "phone")) ?:[];
                    /** @var iMessage $result */
                    foreach ($results as $result){
                        $table[]=[
                            'id'=>$result->id,
                            'message'=>$result->message,
                            'phone'=>$result->phone,
                            'status'=>in_array( $result->phone,$phones)?0:1   ,
                            'token'=>$result->token,
                            'get_number'=>$result->get_number,
                            'createdTime'=> $result->createdTime,
                            'updatedTime'=>$result->updatedTime,
                            'created_at'=>$result->created_at,
                            'updated_at'=>$result->updated_at
                        ];
                    }
                }

            }else{
                /** @var iMessage $result */
                foreach ($results as $result){
                    $table[]=[
                        'id'=>$result->id,
                        'message'=>$result->message,
                        'phone'=>$result->phone,
                        'status'=>$result->status   ,
                        'token'=>$result->token,
                        'get_number'=>$result->get_number,
                        'createdTime'=> $result->createdTime,
                        'updatedTime'=>$result->updatedTime,
                        'created_at'=>$result->created_at,
                        'updated_at'=>$result->updated_at
                    ];
                }
            }
        }
        return  $this->success('Success', [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'tableData' => $table,
            'settings'=>[
                'preg'=> $cache->get('iMessage_preg') ?:'\b\d{11}\b',
                'pattern'=>$cache->get('iMessage_pattern')?:"auto" ,
                'random'=>$cache->get('iMessage_random')?:false,
                'emojiList'=>$cache->get('iMessage_emojiList')?:"😀😃😄😁😆🥹😅😂🤣🥲☺️😊😇🙂🙃😉😌😍🥰😘😗😙😚😋😛😝😜🤪🤨🧐🤓😎🥸🤩🥳😏😒😔😟😕🙁☹️😣😖😫😩🥺😢😭😤😠😡🤬🤯😳🥵🥶😶‍🌫️😱😨😰😥😓🤗🤔🫣🤭🫢🫡🤫🫠🤥😶🫥😐🫤😑🫨😬🙄😯😦😧😮😲🥱😴🤤😪😮‍💨😵😵‍💫🤐🥴🤢🤮🤧😷🤒🤕"
            ],
            'token'=>$cache->get('iMessage_token')?:[]
        ]);
    }

    public function create(){
        $request =Yii::$app->request;
        $model  = new iMessage();
        $data = $request->post('iMessage');
        $model->message = $this->randomStr( stripslashes($data['message']));
        $model->phone =  stripslashes($data['phone']);
        $model->status = $data['status'];
        $model->token = $data['token'];
        if( $model->save()){
            return $this->success();
        }else{
            return  $this->error('Error',$model->getErrors());
        }

    }

    public function update(){
        $request = Yii::$app->request;
        $data =$request->post();
        if(isset($data['iMessage']['id'])){
            $id =$data['iMessage']['id'];
            $model = iMessage::findOne($id);
            if($model){
                $model->phone = $data['iMessage']['phone'];
                $model->message =$data['iMessage']['message'];
                $model->status =$data['iMessage']['status'];
                if($model->save()){
                    return $this->success();
                }
            }
        }
        return  $this->error();


    }

    public function delete(){
        try{
            $request = Yii::$app->request;
            $ids = $request->post('ids',[]);
            foreach ($ids as $id){
                iMessage::deleteAll(['id'=>$id]);
            }
            return $this->success();
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function reset(){
        $request = Yii::$app->request;
        $status = $request->post('status');
        if(iMessage::updateAll(['status'=>$status])){
            return $this->success();
        }
        return $this->error();
    }

    public function adds(){
        $request = Yii::$app->request;
        $cache =Yii::$app->cache;
        $data =$request->post();
        $phone =  $data['iMessage']['phone'];
        $token = $data['iMessage']['token'];
        $type = Yii::$app->cache->get('iMessage_pattern') ?: 'auto';
        $message =stripslashes( $data['iMessage']['message']);
        $status = (int) $data['iMessage']['status'];
        $preg = $cache->get('iMessage_preg') ?:'\b\d{11}\b';
        $is = preg_match_all('/'.$preg.'/',(string) $phone,$result);
        if(!$is){
            return  $this->error('没有匹配到数据');
        }
        $phones = $result[0] or [];
        if(is_array($phones) and !empty($phones)){
            $total =count($phones);
            $phones =array_unique($phones);
            $success =0;
            if($type =='high'){
                $imessage =Yii::$app->cache->get("iMessage_data")?:[];
                foreach ($phones as $item){
                    $imessage[] =[
                        'phone'=> $item,
                        'message'=>$this->randomStr($message)
                    ];
                }
                Yii::$app->cache->set("iMessage_data",$imessage);
            }
            foreach ($phones as $item){
                $model = new iMessage();
                $model->phone = $item;
                $model->message =$this->randomStr( $message);
                $model->token = $token;
                $model->status = $status;
                if($model->save()){
                    $success++;
                }else{
                    return $this->error('Error',$model->getErrors());
                }
            }
            return $this->success('Success',['total'=>$total,'success'=>$success]);
        }

    }

    public function settings(){
        $request = Yii::$app->request;
        $cache =Yii::$app->cache;
        $data =$request->post('iMessage');
        $random = $data['random']== "true";
        $cache->set('iMessage_preg',stripslashes($data['preg'])) ;
        $cache->set('iMessage_pattern', $data['pattern']) ;
        $cache->set('iMessage_random',  $random ) ;
        $cache->set('iMessage_emojiList',stripslashes( $data['emojiList'])) ;
        return $this->success('Success',[
            'preg'=>$cache->get('iMessage_preg'),
            'pattern'=>$cache->get('iMessage_pattern'),
            'random'=>$cache->get('iMessage_random'),
            'emojiList'=>$cache->get('iMessage_emojiList'),
            'post'=>$_POST
        ]);
    }

    public function resetDb(){
        try {
            $cache =Yii::$app->cache;
            $type = $cache->get('iMessage_pattern') ?: 'auto';
            if( $type=='high'){
                $cache->set('iMessage_data',[]);
            }
            $tableName = "wp_imessage";
            iMessage::deleteAll();
            Yii::$app->db->createCommand("ALTER TABLE $tableName AUTO_INCREMENT = 1")->execute();
            return $this->success();
        }catch (Exception $exception){
            return  $this->error();
        }

    }

    public function deleteAllYiFaSong(){
        try {
            $cache =Yii::$app->cache;
            $type = $cache->get('iMessage_pattern') ?: 'auto';
            if($type=='high'){
                iMessage::deleteAll(['NOT IN', 'phone', $cache->get('iMessage_data')?:[]]);
            }
            iMessage::deleteAll(['status'=>1]);
            return $this->success();
        }catch (Exception $exception){
            return  $this->error();
        }
    }

    public  function randomStr($str){
        $cache =Yii::$app->cache;
        $emojiList =$cache->get('iMessage_emojiList')?:"ðððððð¥¹ððð¤£ð¥²☺️ðððððððð¥°ððððððððð¤ªð¤¨ð§ð¤ðð¥¸ð¤©ð¥³ðððððð☹️ð£ðð«ð©ð¥ºð¢ð­ð¤ð ð¡ð¤¬ð¤¯ð³ð¥µð¥¶ð¶‍ð«️ð±ð¨ð°ð¥ðð¤ð¤ð«£ð¤­ð«¢ð«¡ð¤«ð« ð¤¥ð¶ð«¥ðð«¤ðð«¨ð¬ðð¯ð¦ð§ð®ð²ð¥±ð´ð¤¤ðªð®‍ð¨ðµðµ‍ð«ð¤ð¥´ð¤¢ð¤®ð¤§ð·ð¤ð¤";
        if($cache->get('iMessage_random')){
            $emojiArray = preg_split('//u', $emojiList, -1, PREG_SPLIT_NO_EMPTY);
            $emojiCount = count($emojiArray);
            mt_srand(); // 设置随机数种子

            if (strpos($str, '{emoji}') !== false) {
                $randomIndex = mt_rand(0, $emojiCount - 1);
                $randomEmoji = $emojiArray[$randomIndex];
                return str_replace('{emoji}', $randomEmoji, $str);
            } else {
                $randomIndex = mt_rand(0, strlen($str));
                $randomEmojiIndex = mt_rand(0, $emojiCount - 1);
                $randomEmoji = $emojiArray[$randomEmojiIndex];
                return mb_substr($str, 0, $randomIndex) . $randomEmoji . mb_substr($str, $randomIndex);
            }
        }
        return $str;
    }

}
