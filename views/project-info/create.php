<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectInfo */

$this->title = 'Create Project Info';
$this->params['breadcrumbs'][] = ['label' => 'Project Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
