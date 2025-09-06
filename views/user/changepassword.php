<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Thay đổi mật khẩu';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>


        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>


</div>
