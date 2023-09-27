<?php

namespace imessage\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wp_imessage".
 *
 * @property int $id
 * @property int $phone 手机号
 * @property string|null $message 消息
 * @property int|null $status 支付状态
 * @property int|null $token 支付状态
 * @property int|null $get_number 获取次数
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 * @property-read string|null $updatedTime
 * @property-read string|null $createdTime
 */
class iMessage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    # 修改之前
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                #设置默认值
                'value' => time()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_imessage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone', 'status', 'created_at', 'updated_at','status'], 'integer'],
            [['get_number'], 'integer', 'min' => 0],
            [['message','phone','token'], 'string'],
            [['status'], 'default', 'value' => 0],
            [['phone'], 'unique'],
        ];
    }


    public function getUpdatedTime(){
        return date('Y-m-d H:i:s', (int) $this->updated_at) ;
    }

    public function getCreatedTime(){
        return date('Y-m-d H:i:s', (int)$this->created_at) ;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'phone' => Yii::t('app', 'Phone'),
            'message' => Yii::t('app', 'Message'),
            'token' => Yii::t('app', 'Token'),
            'get_number' => Yii::t('app', 'Get Number'),
                'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

}
