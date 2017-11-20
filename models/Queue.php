<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "queue".
 *
 * @property integer $id
 * @property integer $template_id
 * @property string $send_to
 *
 * @property Template $template
 */
class Queue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'queue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'send_to'], 'required'],
            [['template_id'], 'integer'],
            [['send_to'], 'string'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'send_to' => 'Send To',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
