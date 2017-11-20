<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class TestTemplateForm extends Model
{
    public $template;
    public $list;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['template', 'list'], 'required'],
            [['template'], 'exist', 'skipOnError' => true, 'targetClass' => Template::className(), 'targetAttribute' => ['template' => 'id']],
            [['list'], 'validateList']
        ];
    }


    public function validateList($attribute, $params, $validator)
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'Array expected');
        }

        $pattern_url = '/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i';
        $pattern_email = '/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/';
        foreach ($this->$attribute as $data) {

            $email = $data['email'];
            $link = isset($data['link']) ? $data['link'] : false;

            if (
                !preg_match($pattern_email, $email) ||
                ($link !== false && !preg_match($pattern_url, $link))
            ) {
                $this->addError($attribute, 'Format error');
            }
        }
    }
}
