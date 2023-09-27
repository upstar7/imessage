<?php

namespace imessage\controllers\api;

use Yii;
use imessage\models\EMail;
use imessage\models\AppleId;
use imessage\models\PhoneSms;
use imessage\models\Task;
use yii\helpers\ArrayHelper;
class AppleController extends BaseController
{

    /**
     * 获取
     * @return false|string
     */
    public function actionIndex()
    {
        if ($this->auth()) {
            /** @var Task $task */
            $task = Task::find()->where(['status' => 1])->one();

            if ($task and $task->number > 0 and $task->active_number < $task->number) {

                /** @var PhoneSms $phone */
                $phone = PhoneSms::find()
                    ->where(['<>', 'status', 0])
                    ->orderBy(['success_number' => SORT_ASC])
                    ->orderBy(['get_number' => SORT_ASC])
                    ->orderBy(['updated_at' => SORT_ASC])
                    ->one();
                /** @var EMail $mail */
                $mail = EMail::find()
                    ->where(['<>', 'status', 0])
                    ->orderBy(['updated_at' => SORT_ASC])
                    ->one();
                if ($phone and $mail) {
                    $data = [
                        'apple_id' => $mail->email,
                        'first_name' => $this->getFirstName(),
                        'last_name' => $this->getLastName(),
                        'date_of_birth' => $this->dateOfBirth(),
                        'country' => $this->country(),
                        'apple_password' => $this->getPassword(),
                        'phone_id' => $phone->id,
                        'phone' => substr((string)$phone->phone, -10),
                        'phone_country' => $phone->phone_country,
                        'phone_url' => $phone->phone_url,
                        'email_id' => $mail->id,
                        'email' => $mail->email,
                        'email_password' => $mail->email_password,
                        'email_host' => $mail->email_host,
                        'email_port' => $mail->email_port,
                    ];
                    $mail->get_number++;
                    $phone->get_number++;
                    $phone->updated_at = time();
                    $mail->updated_at = time();
                    $phone->save(false, ['updated_at', 'get_number']);
                    $mail->save(false, ['updated_at', 'get_number']);
                    return $this->success('Success', $data);
                } else {
                    return $this->error("后台没有可使用的数据");
                }

            }
            return $this->error('没有注册任务');
        }else{
            return  $this->error('你没有访问权限');
        }
    }


    /**
     * 新增
     * @return false|string|void
     */
    public function actionCreate()
    {
        $request = \Yii::$app->request;
        try {
            if ($this->auth()) {
                $type = $request->post('type', '');
                if (!empty($type) and $type == 'update') {
                    /** @var AppleId $model */
                    $model = AppleId::find()->where(['id' => $request->post('id')])->one();
                    $apple_password = $request->post('apple_password','');
                    if ($model) {
                        $model->status = "0";
                        $model->notes = $request->post('notes');
                        if(!empty($apple_password)){
                            $model->apple_password = $apple_password;
                        }
                        if ($model->update()) {
                            return $this->success();
                        }
                    }
                    return $this->error();
                } else {
                    $model = new AppleId();
                    $model->apple_id = $request->post('apple_id');
                    $model->first_name = $request->post('first_name');
                    $model->last_name = $request->post('last_name');
                    $model->date_of_birth = $request->post('date_of_birth');
                    $model->country = $request->post('country');
                    $model->apple_password = $request->post('apple_password');
                    $model->phone = $request->post('phone');
                    $model->phone_country = $request->post('phone_country');
                    $model->phone_url = $request->post('phone_url');
                    $model->email = $request->post('email');
                    $model->email_password = $request->post('email_password');
                    /** @var PhoneSms $phone */
                    $phone = PhoneSms::find()->where(['phone_url' => $request->post('phone_url')])->one();
                    if ($phone) {
                        $phone->status = 0;
                        $phone->save();
                    }
                    /** @var EMail $email */
                    $email = EMail::find()->where(['email' => $request->post('apple_id')])->one();
                    if ($email) {
                        $email->status = 0;
                        $email->save();
                    }
                    if ($model->save()) {
                        $this->task($request->post());
                        $this->logSuccess();
                        return $this->success();
                    } else {
                        return $this->error('Error', $model->getErrors());
                    }
                }
            }
        } catch (\Exception $exception) {
            return $this->error('意外接收', $exception->getMessage());
        }
    }


    /**
     * 日期
     * @return string
     */
    private function dateOfBirth()
    {
        $startDate = strtotime('1970-01-01'); // 起始日期时间戳
        $endDate = strtotime('2005-01-01'); // 结束日期时间戳
        $randomTimestamp = mt_rand($startDate, $endDate); // 生成介于起始日期和结束日期之间的随机时间戳
        return (string)date('Ymd', $randomTimestamp);
    }

    /**
     * 姓
     * @return string
     */
    private function getFirstName()
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'David', 'Sarah'];
        return $firstNames[array_rand($firstNames)];
    }

    /**
     * 名
     * @return string
     */
    private function getLastName()
    {
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis'];
        return $lastNames[array_rand($lastNames)];
    }

    /**
     * 密码
     * @return string
     */
    private function getPassword()
    {
        $lastNames = ['Qq112211..'];
        return $lastNames[array_rand($lastNames)];
    }

    /**
     * @param $ur
     * @param $data
     * @param $token
     * @param $timeout
     * @return void
     */
    public function push($url,$data,$token,$timeout=3)
    {
        if(isset($data['phone_country'])){
            $data['phone_country']='';
        }
        try{
            $headers = [
                'token: ' . $token,
                'Content-Type: application/x-www-form-urlencoded'
            ];

            $options = [
                'http' => [
                    'header' => implode("\r\n", $headers),
                    'method' => 'POST',
                    'timeout' => $timeout,// 超时时间
                    'content' => http_build_query($data)
                ]
            ];

            $context = stream_context_create($options);
            file_get_contents($url, false, $context);
            return true;
        }catch (\Exception $exception){
            return false;
        }


    }

    public function task($post)
    {
        /** @var Task $task */
        $task = Task::find()->where(['status' => 1])->one();
        $path = \Yii::getAlias('@runtime')."/task";
        if ($task ) {
            $task_name = $task->name;
            $type =$task->notice_type;
            $url =$task->notice_url;
            $token =$task->notice_token;
            $mail =$task->notice_mail;
            $task->active_number++;
            if( $task->active_number>= $task->number){
                $task->status = 0;
            }
            $task->save();
            $str = $post["apple_id"].'----'.$post['apple_password'].'----'.$post['phone'].'----'.$post['phone_url'];
            $this->taskLog($task_name, $str );
            if($type =='http'){
                $this->push($url,$post,$token);
            }elseif ($type=="mail"){
                wp_mail($mail,'通知',$str.PHP_EOL);
            }

        }
    }

    public function taskLog($task_name, $str ){
        $path = \Yii::getAlias('@runtime')."/task/".$task_name.".txt";
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        // 将内容追加到文件
        file_put_contents($path ,  $str.PHP_EOL, FILE_APPEND);
    }

    /**
     * @return string
     */
    public function country()
    {
        $country = [
            "阿尔巴尼亚", "阿尔及利亚", "阿富汗", "阿根廷", "阿拉伯联合酋长国",
            "阿鲁巴", "阿曼", "阿塞拜疆", "埃及", "埃塞俄比亚", "爱尔兰",
            "爱沙尼亚", "安道尔", "安哥拉", "安圭拉岛", "安提瓜和巴布达", "奥地利",
            "奥兰群岛", "澳大利亚", "澳门", "巴巴多斯", "巴布亚新几内亚", "巴哈马",
            "巴基斯坦", "巴拉圭", "巴勒斯坦领土", "巴林", "巴拿马", "巴西",
            "白俄罗斯", "百慕大", "保加利亚", "北马里亚纳群岛", "北马其顿", "贝宁",
            "比利时", "冰岛", "玻利维亚", "波多黎各", "波兰", "波斯尼亚和黑塞哥维那",
            "博茨瓦纳", "伯利兹", "不丹", "布基纳法索", "布隆迪", "布韦岛",
            "查戈斯群岛", "赤道几内亚", "丹麦", "德国", "东帝汶", "多哥",
            "多米尼加共和国", "多米尼克", "俄罗斯", "厄瓜多尔", "厄立特里亚",
            "法国", "法罗群岛", "法属波利尼西亚", "法属圭亚那", "法属南部领地",
            "菲律宾", "芬兰", "佛得角", "福克兰群岛", "冈比亚", "刚果共和国",
            "刚果民主共和国", "哥伦比亚", "哥斯达黎加", "格林纳达", "格陵兰",
            "格鲁吉亚", "根西岛", "瓜德罗普", "关岛", "圭亚那", "哈萨克斯坦",
            "海地", "韩国", "荷兰", "荷属加勒比区", "荷属圣马丁", "赫德岛和麦克唐纳群岛",
            "黑山", "洪都拉斯", "基里巴斯", "吉布提", "吉尔吉斯斯坦", "几内亚",
            "几内亚比绍", "加拿大", "加纳", "加蓬", "柬埔寨", "捷克", "津巴布韦",
            "喀麦隆", "卡塔尔", "开曼群岛", "科科斯（基林）群岛", "科摩罗", "科索沃",
            "科特迪瓦", "科威特", "克罗地亚", "肯尼亚", "库克群岛", "库拉索", "拉脱维亚",
            "莱索托", "老挝", "黎巴嫩", "利比里亚", "利比亚", "立陶宛", "列支敦士登",
            "留尼汪", "卢森堡", "卢旺达", "罗马尼亚", "马达加斯加", "马耳他", "马尔代夫",
            "马拉维", "马来西亚", "马里", "马绍尔群岛", "马提尼克", "马约特", "曼恩岛",
            "毛里求斯", "毛里塔尼亚", "美国", "美国本土外小岛屿", "美属萨摩亚", "美属维尔京群岛",
            "蒙古", "蒙特塞拉特", "孟加拉国", "秘鲁", "密克罗尼西亚", "缅甸", "摩尔多瓦",
            "摩洛哥", "摩纳哥", "莫桑比克", "墨西哥", "纳米比亚", "南非", "南极洲",
            "南乔治亚和南桑威奇群岛", "南苏丹", "尼泊尔", "尼加拉瓜", "尼日尔", "尼日利亚",
            "纽埃", "挪威", "诺福克岛", "帕劳", "皮特凯恩", "葡萄牙", "日本", "瑞典",
            "瑞士", "萨尔瓦多", "萨摩亚", "塞尔维亚", "塞拉利昂", "塞内加尔", "塞浦路斯",
            "塞舌尔", "沙特阿拉伯", "圣巴泰勒米", "圣诞岛", "圣多美和普林西比", "圣赫勒拿",
            "圣基茨和尼维斯", "圣卢西亚", "圣马丁岛", "圣马力诺", "圣皮埃尔和密克隆群岛",
            "圣文森特和格林纳丁斯", "斯里兰卡", "斯洛伐克", "斯洛文尼亚", "斯瓦尔巴岛和扬马延岛",
            "斯威士兰", "苏丹", "苏里南", "索马里", "所罗门群岛", "塔吉克斯坦", "台湾",
            "泰国", "坦桑尼亚", "汤加", "特克斯和凯科斯群岛", "特立尼达和多巴哥", "突尼斯",
            "图瓦卢", "土耳其", "土库曼斯坦", "托克劳", "瓦利斯群岛和富图纳群岛", "瓦努阿图",
            "危地马拉", "委内瑞拉", "文莱", "乌干达", "乌克兰", "乌拉圭", "乌兹别克斯坦",
            "西班牙", "西撒哈拉", "希腊", "香港", "新加坡", "新喀里多尼亚", "新西兰",
            "匈牙利", "牙买加", "亚美尼亚", "也门", "伊拉克", "以色列", "意大利", "印度",
            "印度尼西亚", "英国", "英属维尔京群岛", "约旦", "越南", "赞比亚", "泽西岛",
            "乍得", "直布罗陀", "智利", "中非共和国", "中国大陆", "瑙鲁", "梵蒂冈", "斐济"];
        $randomKey = array_rand($country);
        return $country[$randomKey];
    }

    /**
     * @return string
     */
    public function phones()
    {

        $phones = [
            "+355 (阿尔巴尼亚)", "+213 (阿尔及利亚)", "+93 (阿富汗)", "+54 (阿根廷)",
            "+971 (阿拉伯联合酋长国)", "+297 (阿鲁巴)", "+968 (阿曼)", "+994 (阿塞拜疆)",
            "+20 (埃及)", "+251 (埃塞俄比亚)", "+353 (爱尔兰)", "+372 (爱沙尼亚)",
            "+376 (安道尔)", "+244 (安哥拉)", "+1 (安圭拉岛)", "+1 (安提瓜和巴布达)",
            "+43 (奥地利)", "+358 (奥兰群岛)", "+61 (澳大利亚)", "+853 (澳门)",
            "+1 (巴巴多斯)", "+675 (巴布亚新几内亚)", "+1 (巴哈马)", "+92 (巴基斯坦)",
            "+595 (巴拉圭)", "+970 (巴勒斯坦领土)", "+973 (巴林)", "+507 (巴拿马)",
            "+55 (巴西)", "+375 (白俄罗斯)", "+1 (百慕大)", "+359 (保加利亚)",
            "+1 (北马里亚纳群岛)", "+389 (北马其顿)", "+229 (贝宁)", "+32 (比利时)",
            "+354 (冰岛)", "+591 (玻利维亚)", "+1 (波多黎各)", "+48 (波兰)",
            "+387 (波斯尼亚和黑塞哥维那)", "+267 (博茨瓦纳)", "+501 (伯利兹)",
            "+975 (不丹)", "+226 (布基纳法索)", "+257 (布隆迪)", "+47 (布韦岛)",
            "+246 (查戈斯群岛)", "+240 (赤道几内亚)", "+45 (丹麦)", "+49 (德国)",
            "+670 (东帝汶)", "+228 (多哥)", "+1 (多米尼加共和国)", "+1 (多米尼克)",
            "+7 (俄罗斯)", "+593 (厄瓜多尔)", "+291 (厄立特里亚)", "+33 (法国)",
            "+298 (法罗群岛)", "+689 (法属波利尼西亚)", "+594 (法属圭亚那)",
            "+262 (法属南部领地)", "+63 (菲律宾)", "+358 (芬兰)", "+238 (佛得角)",
            "+500 (福克兰群岛)", "+220 (冈比亚)", "+242 (刚果共和国)",
            "+243 (刚果民主共和国)", "+57 (哥伦比亚)", "+506 (哥斯达黎加)",
            "+1 (格林纳达)", "+299 (格陵兰)", "+995 (格鲁吉亚)", "+44 (根西岛)",
            "+590 (瓜德罗普)", "+1 (关岛)", "+592 (圭亚那)", "+7 (哈萨克斯坦)",
            "+509 (海地)", "+82 (韩国)", "+31 (荷兰)", "+599 (荷属加勒比区)",
            "+1 (荷属圣马丁)", "+61 (赫德岛和麦克唐纳群岛)", "+382 (黑山)",
            "+504 (洪都拉斯)", "+686 (基里巴斯)", "+253 (吉布提)",
            "+996 (吉尔吉斯斯坦)", "+224 (几内亚)", "+245 (几内亚比绍)",
            "+1 (加拿大)", "+233 (加纳)", "+241 (加蓬)", "+855 (柬埔寨)",
            "+420 (捷克)", "+263 (津巴布韦)", "+237 (喀麦隆)", "+974 (卡塔尔)",
            "+1 (开曼群岛)", "+61 (科科斯（基林）群岛)", "+269 (科摩罗)",
            "+383 (科索沃)", "+225 (科特迪瓦)", "+965 (科威特)", "+385 (克罗地亚)",
            "+254 (肯尼亚)", "+682 (库克群岛)", "+599 (库拉索)", "+371 (拉脱维亚)",
            "+266 (莱索托)", "+856 (老挝)", "+961 (黎巴嫩)", "+231 (利比里亚)",
            "+218 (利比亚)", "+370 (立陶宛)", "+423 (列支敦士登)", "+262 (留尼汪)",
            "+352 (卢森堡)", "+250 (卢旺达)", "+40 (罗马尼亚)", "+261 (马达加斯加)",
            "+356 (马耳他)", "+960 (马尔代夫)", "+265 (马拉维)", "+60 (马来西亚)",
            "+223 (马里)", "+692 (马绍尔群岛)", "+596 (马提尼克)", "+262 (马约特)",
            "+44 (曼恩岛)", "+230 (毛里求斯)", "+222 (毛里塔尼亚)", "+1 (美国)",
            "+1 (美属萨摩亚)", "+1 (美属维尔京群岛)", "+976 (蒙古)", "+1 (蒙特塞拉特)",
            "+880 (孟加拉国)", "+51 (秘鲁)", "+691 (密克罗尼西亚)", "+95 (缅甸)",
            "+373 (摩尔多瓦)", "+212 (摩洛哥)", "+377 (摩纳哥)", "+258 (莫桑比克)",
            "+52 (墨西哥)", "+264 (纳米比亚)", "+27 (南非)", "+672 (南极洲)",
            "+211 (南苏丹)", "+977 (尼泊尔)", "+505 (尼加拉瓜)", "+227 (尼日尔)",
            "+234 (尼日利亚)", "+683 (纽埃)", "+47 (挪威)", "+680 (帕劳)",
            "+870 (皮特凯恩)", "+351 (葡萄牙)", "+81 (日本)", "+46 (瑞典)",
            "+41 (瑞士)", "+503 (萨尔瓦多)", "+685 (萨摩亚)", "+381 (塞尔维亚)",
            "+232 (塞拉利昂)", "+221 (塞内加尔)", "+357 (塞浦路斯)", "+248 (塞舌尔)",
            "+966 (沙特阿拉伯)", "+590 (圣巴泰勒米)", "+61 (圣诞岛)", "+239 (圣多美和普林西比)",
            "+290 (圣赫勒拿)", "+1 (圣基茨和尼维斯)", "+1 (圣卢西亚)", "+1 (圣马丁岛)",
            "+378 (圣马力诺)", "+508 (圣皮埃尔和密克隆群岛)", "+1 (圣文森特和格林纳丁斯)",
            "+94 (斯里兰卡)", "+421 (斯洛伐克)", "+386 (斯洛文尼亚)", "+47 (斯瓦尔巴岛和扬马延岛)",
            "+268 (斯威士兰)", "+249 (苏丹)", "+597 (苏里南)", "+252 (索马里)",
            "+677 (所罗门群岛)", "+992 (塔吉克斯坦)", "+886 (台湾)", "+66 (泰国)",
            "+255 (坦桑尼亚)", "+676 (汤加)", "+1 (特克斯和凯科斯群岛)", "+1 (特立尼达和多巴哥)",
            "+216 (突尼斯)", "+688 (图瓦卢)", "+90 (土耳其)", "+993 (土库曼斯坦)",
            "+690 (托克劳)", "+681 (瓦利斯群岛和富图纳群岛)", "+678 (瓦努阿图)",
            "+502 (危地马拉)", "+58 (委内瑞拉)", "+673 (文莱)", "+256 (乌干达)",
            "+380 (乌克兰)", "+598 (乌拉圭)", "+998 (乌兹别克斯坦)", "+34 (西班牙)",
            "+212 (西撒哈拉)", "+30 (希腊)", "+852 (香港)", "+65 (新加坡)",
            "+687 (新喀里多尼亚)", "+64 (新西兰)", "+36 (匈牙利)", "+1 (牙买加)",
            "+374 (亚美尼亚)", "+967 (也门)", "+964 (伊拉克)", "+972 (以色列)",
            "+39 (意大利)", "+91 (印度)", "+62 (印度尼西亚)", "+44 (英国)",
            "+1 (英属维尔京群岛)", "+962 (约旦)", "+84 (越南)", "+260 (赞比亚)",
            "+44 (泽西岛)", "+235 (乍得)", "+350 (直布罗陀)", "+56 (智利)",
            "+236 (中非共和国)", "+86 (中国大陆)", "+674 (瑙鲁)", "+39 (梵蒂冈)", "+679 (斐济)"
        ];
        $matchingElements = [];
        foreach ($phones as $element) {
            if (strpos($element, '+1 (') !== false) {
                $matchingElements[] = $element;
            }
        }


        $randomKey = array_rand($matchingElements);
        return $matchingElements[$randomKey];
    }

    public function logSuccess(){
        try{
            $require = Yii::$app->request;
            $tokens = Yii::$app->cache->get('iMessage_token', []);
            $header = $require->getHeaders();
            $tmp = $tokens[$header['token']];
            $tmp['success_number']++;
            Yii::$app->cache->set('iMessage_token',
                ArrayHelper::merge($tokens, [$header['token'] => $tmp])
            );
        }catch (\Exception $exception){

        }

    }
}