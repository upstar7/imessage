<?php

namespace imessage\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "wp_phone_sms".
 *
 * @property int $id
 * @property string $phone 手机号
 * @property string|null $phone_country 地区
 * @property string|null $phone_url 解码url
 * @property int|null $get_number 获取次数
 * @property int|null $success_number
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class PhoneSms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_phone_sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['phone', 'phone_country', 'phone_url'], 'string', 'max' => 255],
            [['phone'], 'unique'],
            [['get_number','success_number'], 'integer', 'min' => 0]
        ];
    }

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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'phone' => Yii::t('app', 'Phone'),
            'phone_country' => Yii::t('app', 'Phone Country'),
            'phone_url' => Yii::t('app', 'Phone Url'),
            'status' => Yii::t('app', 'Status'),
            'get_number' => Yii::t('app', 'Get Number'),
            'success_number'=>Yii::t('','Success Number'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
