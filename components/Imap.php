<?php

namespace imessage\components;

use yii\base\Component;

/**
 *
 * @property-read int $mailNumber
 * @property-read  array $boxList
 * @property-read $header
 * @property  $box
 */
class Imap extends Component
{
    public $host='mail.ptjiande.com';
    public $port="993";
    public $username='1o5hzidv@ptjiande.com';
    public $password='p58r6f-MJd';
    public $box ="INBOX";
    public $_header;
    public $_box;
    private $imap;



    public function connect()
    {
        $this->imap = imap_open('{mail.ptjiande.com:993/ssl/novalidate-cert}', $this->username, $this->password) or die('Cannot connect to mailbox: ' . imap_last_error());
    }

    /**
     * @return array|false
     */
    public function getBoxList()
    {
        return imap_list($this->imap, "{" . $this->host . ":" . $this->port . "}", "*");
    }

    /**
     * @return mixed
     */
    public function getHeader(){
        return $this->_header;
    }

    /**
     * @param $name
     * @return bool
     */
    public function getBox($name ='INBOX'){
        if($this->_box){
            return $this->_box;
        }else{
            return imap_reopen($this->imap,
                "{" . $this->host . ":" . $this->port . "/ssl/novalidate-cert}".$name);
        }

    }

    /**
     * @param $value
     * @return void
     */
    public function setBox($value=""){
        $this->_box = imap_reopen($this->imap,
            "{" . $this->host . ":" . $this->port . "/ssl/novalidate-cert}".$value
        ) ;

    }

    /**
     *
     * @param int $index
     * @return $this|false
     */
    public function getMailOne($index=''){
        if(empty( $index)){
            $index = $this->mailNumber;
        }
        if($index>0 and $index<= $this->mailNumber){
            $this->_header =imap_header($this->imap, $index);
            return $this;
        }
        return false;
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function getHeaderArray($index){
        $arr = json_encode(imap_header($this->imap, $index));
        return json_decode($arr,true);
    }

    /**
     * @return false|int
     */
    public function  getMailNumber(){
        return imap_num_msg($this->imap);
    }

    /**
     * @return string
     */
    public function getMailFrom(){
        return $this->header->from[0]->mailbox . "@" .  $this->header->from[0]->host;
    }

    /**
     * @return string
     */
    public function getMailTo(){
       return  $this->header->to[0]->mailbox . "@" . $this->header->to[0]->host;
    }

    /**
     * @return string
     */
    public function getMailSubject(){
        return imap_utf8( $this->header->subject);
    }

    public function getMailBody(){

    }

    public function close (){
        imap_close($this->imap);
    }

}