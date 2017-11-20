<?php

namespace app\controllers;

use Yii;
use app\models\Email;
use yii\web\Controller;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class EmailController extends Controller
{

    public function actions()
    {
        return [
            'autocomplete' => [
                'class' => 'app\controllers\actions\AutocompleteAction',
                'tableName' => Email::tableName(),
                'select_fields' => 'email, link',
                'field' => 'email'
            ]
        ];
    }

    public function actionLink()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $email = Yii::$app->request->post('email');
        $model = $this->findModel($email);
        return $model->link;
    }

    public function actionEraseLink()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $email = Yii::$app->request->post('email');
        $model = $this->findModel($email);
        $model->link = '';
        if ($model->update(true, ['link']) === false) {
            throw new \yii\web\BadRequestHttpException(Json::encode($model->getFirstErrors()));
        }
        return ['status' => 'ok'];
    }

    /**
     * Finds the Email model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $email
     * @return Email the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($email)
    {
        if (($model = Email::findOne(['email' => $email])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested model does not exist.');
        }
    }

}
