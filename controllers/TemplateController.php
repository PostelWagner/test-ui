<?php

namespace app\controllers;

use Yii;
use app\models\Template;
use app\models\TemplateSearch;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TestTemplateForm;
use app\models\Email;
use app\models\Queue;
use yii\helpers\Json;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
{
    /**
     * Lists all Template models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionTest()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $test_model = new TestTemplateForm();

        if ($test_model->load(Yii::$app->request->post(), '') && $test_model->validate()) {
            foreach ($test_model->list as $data) {
                $email_model = Email::find()->where(['email' => $data['email']])->one();
                if (!$email_model) {
                    $email_model = new Email([
                        'email' => $data['email']
                    ]);

                }
                if (isset($data['link'])) {
                    $email_model->link = $data['link'];
                }
                if (!$email_model->save()) {
                    throw new \yii\web\BadRequestHttpException(Json::encode($email_model->getFirstErrors()));
                }
            }
            $queue = new Queue([
                'template_id' => $test_model->template,
                'send_to' => Json::encode($test_model->list),
            ]);
            $queue->save();
            Template::updateAll(['last_test' => new Expression('NOW()')], ['id' => $test_model->template]);

            Yii::$app->session->setFlash('success', "Template #{$test_model->template} has been sent successfully");

            return ['status' => 'ok'];
        } else {
            throw new \yii\web\BadRequestHttpException(Json::encode($test_model->getFirstErrors()));
        }
    }

    /**
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
