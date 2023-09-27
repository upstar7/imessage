<?php

namespace imessage\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "wp_email".
 *
 * @property int $id
 * @property string $email 邮箱
 * @property string|null $email_password 密码
 * @property string|null $email_url 解码码url
 * @property string|null $email_host 服务器
 * @property int|null $email_port 端口号
 * @property int|null $get_number 获取次数
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class EMail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_email';
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
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email_port', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'email_password', 'email_url', 'email_host'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['get_number'], 'integer', 'min' => 0]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'email_password' => Yii::t('app', 'Email Password'),
            'email_url' => Yii::t('app', 'Email Url'),
            'email_host' => Yii::t('app', 'Email Host'),
            'email_port' => Yii::t('app', 'Email Port'),
            'get_number' => Yii::t('app', 'Get Number'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
