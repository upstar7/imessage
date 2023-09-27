<?php

namespace imessage\controllers\api;

use imessage\models\EMail;
use Yii;

class ImapController extends BaseController
{
    public function actionIndex()
    {
        if($this->auth()){
            $id = Yii::$app->request->get('id');
            /** @var EMail $model */
            $model = EMail::find()->where(['id'=>$id])->one();
            $determineResponseType = $this->determineResponseType();
            if($model){
                $server = '{'.$model->email_host.':'.$model->email_port.'/imap/ssl/novalidate-cert}INBOX';
                $username = $model->email;
                $password = $model->email_password;

                $imap = imap_open($server, $username, $password);

                if (!$imap) {
                    if($determineResponseType =='json'){
                        return $this->error(imap_last_error());
                    }
                    return  $this->html(imap_last_error());
                }
                // 使用 imap_search 函数搜索最新的一封邮件
                $mailIds =imap_search($imap, 'ALL');
                if ($mailIds) {
                    //获取第一封(最新的)电子邮件ID
                    $latestMailId = reset($mailIds);

                    // 获取邮件的头部信息
                    $header = imap_fetchheader($imap, $latestMailId);

                    // 解析头部信息以提取所需的信息
                    $headers = imap_rfc822_parse_headers($header);

                    $from = $headers->from[0]->mailbox . "@" . $headers->from[0]->host;
                    $to = $headers->to[0]->mailbox . "@" . $headers->to[0]->host;
                    $subject =mb_decode_mimeheader( $headers->subject);
                    $date = isset($headers->date) ? $headers->date : '';

                    // 获取邮件的结构信息
                    $structure = imap_fetchstructure($imap, $latestMailId);

                    // 初始化存储邮件正文的数组
                    $body = array();

                    // 解析邮件结构，将邮件正文保存到数组
                    foreach ($structure->parts as $partNumber => $part) {
                        if($partNumber>0){
                            $body[$partNumber] =  $this-> parseBodyParts($imap, $latestMailId, $partNumber,$structure);
                        }
                    }
                    if($determineResponseType =='json'){
                        return $this->success('Success',[
                            'from'=>$from,
                            'to'=>$to,
                            'subject'=>$subject,
                            'date'=>date("Y-m-d H:i:s", strtotime($date)),
                            'body'=>$body,
                        ]);
                    }else{
                        return $this->html(join(PHP_EOL,$body));
                    }

                } else {
                    if($determineResponseType =='json'){
                        return $this->error('没有找到邮件');
                    }else{
                        return $this->html('没有找到邮件');
                    }
                }
            }
            if($determineResponseType =='json'){
                return $this->error('记录不存在');
            }else{
                return $this->html('记录不存在');
            }
        }

    }

    public function determineResponseType()
    {
        $acceptHeader = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;

        // Check if the "Accept" header contains "application/json" with a higher priority than "text/html"
        if (strpos($acceptHeader, 'application/json') !== false &&
            (
                strpos($acceptHeader, 'application/json') < strpos($acceptHeader, 'text/html') ||
                strpos($acceptHeader, 'text/html') === false
            )
        ) {
            return 'json'; // JSON response is preferred
        } else if (strpos($acceptHeader, 'text/html') !== false) {
            return 'html'; // HTML response is preferred
        } else {
            return 'unknown'; // No specific type is specified or it's not recognizable
        }
    }


    /**
     * @param resource $imap
     * @param int $mailId
     * @param object $structure
     * @param array $body
     * @return void
     */
    public function parseBodyParts($imap, $mailId, $partNumber,&$structure)
    {

        $partBody = imap_fetchbody($imap, $mailId, $partNumber);
        $partEncoding = $structure->parts[$partNumber - 1]->encoding;

        // Decode the part based on encoding
        switch ($partEncoding) {
            case 0:
                // 7BIT encoding
                $body = imap_utf8($partBody);
                break;
            case 1:
                // 8BIT encoding
                $body = imap_utf8($partBody);
                break;
            case 2:
                // BINARY encoding, no conversion needed
                $body = $partBody;
                break;
            case 3:
                // BASE64 encoding
                $body = base64_decode($partBody);
                break;
            case 4:
                // QUOTED-PRINTABLE encoding
                $body = quoted_printable_decode($partBody);
                break;
            default:
                // Unknown encoding, treat as plain text
                $body = $partBody;
                break;
        }


       return $body;

    }

}
