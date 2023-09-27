<?php

namespace imessage\models;

use Yii;

/**
 * This is the model class for table "wp_task".
 *
 * @property int $id
 * @property string $name 任务名称
 * @property int|null $number 数量
 * @property int|null $active_number 已完成数量
 * @property string|null $notice_type 通知类型
 * @property string|null $notice_url 通知url
 * @property string|null $notice_token 通知token
 * @property string|null $notice_mail 通知邮箱
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['number', 'active_number', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'notice_type', 'notice_url', 'notice_token', 'notice_mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'number' => Yii::t('app', 'Number'),
            'active_number' => Yii::t('app', 'Active Number'),
            'notice_type' => Yii::t('app', 'Notice Type'),
            'notice_url' => Yii::t('app', 'Notice Url'),
            'notice_token' => Yii::t('app', 'Notice Token'),
            'notice_mail' => Yii::t('app', 'Notice Mail'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
