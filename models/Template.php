<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "template".
 *
 * @property integer $id
 * @property string $subject
 * @property string $body
 * @property integer $last_test
 *
 * @property Queue[] $queues
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'body'], 'required'],
            [['body'], 'string'],
            [['last_test'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'body' => 'Body',
            'last_test' => 'Last Test',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueues()
    {
        return $this->hasMany(Queue::className(), ['template_id' => 'id']);
    }
}
