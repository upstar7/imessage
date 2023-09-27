<?php

namespace imessage\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;


/**
 * This is the model class for table "wp_apple_id".
 *
 * @property int $id
 * @property string $apple_id Apple ID
 * @property string|null $first_name 姓
 * @property string|null $last_name 名
 * @property string|null $date_of_birth 出生日期
 * @property string|null $country 地区
 * @property string|null $apple_password 密码
 * @property string|null $phone 手机号
 * @property string|null $phone_country 手机号
 * @property string|null $phone_url 解码url
 * @property string|null $email 邮箱
 * @property string|null $email_password 邮箱密码
 * @property string|null $email_url 邮箱解码url
 * @property int|null $get_number 获取次数
 * @property int|null $status 状态
 * @property string|null $notes 备注
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 * @property-read string|null $updatedTime
 * @property-read string|null $createdTime
 */
class AppleId extends \yii\db\ActiveRecord
{
    public $updatedTime;
    public $createdTime;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_apple_id';
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
            [['apple_id','apple_password'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['apple_id', 'first_name', 'last_name', 'date_of_birth', 'country', 'apple_password', 'phone', 'phone_country', 'phone_url', 'email', 'email_password', 'email_url','notes'], 'string', 'max' => 255],
            [['email'],'email'],
            [['apple_id'], 'unique'],
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
            'apple_id' => Yii::t('app', 'Apple ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'date_of_birth' => Yii::t('app', 'Date Of Birth'),
            'country' => Yii::t('app', 'Country'),
            'apple_password' => Yii::t('app', 'Apple Password'),
            'phone' => Yii::t('app', 'Phone'),
            'phone_country' => Yii::t('app', 'Phone Country'),
            'phone_url' => Yii::t('app', 'Phone Url'),
            'email' => Yii::t('app', 'Email'),
            'email_password' => Yii::t('app', 'Email Password'),
            'email_url' => Yii::t('app', 'Email Url'),
            'get_number' => Yii::t('app', 'Get Number'),
            'notes'=>Yii::t('app', 'Notes'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
    public function attributes()
    {
        $key = parent::attributes();
        $key[]='createdTime';
        $key[]='updatedTime';
        return $key;
    }

    public function fields(){
        $fields =parent::fields();
        $fields[]='updatedTime';
        $fields[]='createdTime';
        return $fields;

    }
}
