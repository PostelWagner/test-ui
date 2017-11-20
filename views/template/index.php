<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use dosamigos\typeahead\Bloodhound;
use dosamigos\typeahead\TypeAhead;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="template-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'subject',
            'body:ntext',
            [
                'attribute' => 'last_test',
                'filter' => dosamigos\datepicker\DatePicker::widget([
                        'name' => 'TemplateSearch[last_test]',
                        'model' => $searchModel,
                        'attribute' => 'last_test',
                        'clientOptions' => [
                            'orientation' => 'bottom',
                            'weekStart' => 1,
                            'format' => 'yyyy-mm-dd'
                        ]
                ]),
                'format' => 'html',
            ],
            [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{send}',
                    'buttons' => [
                        'send' => function ($url,$model,$key) {

                            return Html::a(
                                    Html::tag('span', '', ['class' => 'glyphicon glyphicon-envelope']),
                                    '#',
                                    [
                                        'data-template' => $model->id,
                                        'data-toggle' => 'modal',
                                        'data-target' => '#myModal'
                                    ]
                            );
                        }
                    ]
            ],
        ],
    ]); ?>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Template #<span id="template_num"></span>. Test parameters</h4>
            </div>
            <?php $form = ActiveForm::begin([
                    'id' => 'test-params',
                    'action' => '/template/test'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <span class="header_col">Email</span>
                    </div>
                    <div class="col-md-6">
                        <span class="header_col">Link</span>
                    </div>
                    <div class="col-md-1">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>