<?php

namespace imessage\components;

use yii\base\Component;

class OkxComponent extends Component
{
    public $APIKey = '';
    public $SecretKey = '';
    public $Passphrase = '';

    public $url='https://www.okx.com';

    /**
     * @param $timestamp
     * @param $method
     * @param $requestPath
     * @param $body
     * @return string
     */
    private function sign($timestamp  ,$method ,$requestPath , $body){
        return base64_encode(hash_hmac('sha256', $timestamp.$method.$requestPath.$body, $this->SecretKey, true));
    }

    public function conn($method ,$requestPath , $body=[],$timeout=3){
        $timestamp = gmdate('Y-m-d\TH:i:s\.v\Z', time());
        if( $method=='GET'){
            $requestPath = $requestPath."?".http_build_query($body);
            $body = '';
        }else{
            if(!empty($body)){
                $body =json_decode($body,true);
            }else{
                $body ='';
            }

        }

        $header =[
            'OK-ACCESS-KEY'=>$this->APIKey,
            'OK-ACCESS-SIGN'=>$this->sign($timestamp  ,$method ,$requestPath , $body),
            'OK-ACCESS-TIMESTAMP'=>$timestamp,
            'OK-ACCESS-PASSPHRASE'=>$this->Passphrase,
            'Content-Type'=>"application/json",
            'x-simulated-trading'=> '1',
            'Content-Length'=>strlen($body)
        ];
        $herders=[];
        foreach ($header as $key =>$value){
            $herders[]= $key.": ".$value;
        }
        $options = [
            'http' => [
                'header' => join("\r\n", $herders),
                'method' => $method,
                'timeout' => $timeout,
                'content' => $body
            ]
        ];
        $context = stream_context_create($options);
        return file_get_contents($this->url.$requestPath,false, $context);
    }




}