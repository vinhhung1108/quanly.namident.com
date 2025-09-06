<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\User;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>	
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top my-navbar',
        ],
    ]);
	$items=[];
	$items[]= ['label' => 'Home', 'url' => ['/site/index']];
	if(!Yii::$app->user->isGuest){$items[]= ['label' => 'Danh sách điều trị', 'url' => ['/dieu-tri/index']];}
	if(!Yii::$app->user->isGuest){$items[]= ['label' => 'Quản lý Khách hàng', 'url' => ['/khach-hang/index']];}
	if(!Yii::$app->user->isGuest){$items[]= ['label' => 'Thu Chi',
				'items'=>[
					['label' => 'Quản lý Thu Chi', 'url' => ['/thu-chi']],
					['label' => 'Loại Thu Chi', 'url' => ['/loai-thu-chi']],
          ['label' => 'Báo cáo thu', 'url' => ['/bao-cao-thu']],
          ['label' => 'Người thu/chi', 'url' => ['/nguoi-thu-chi/index']],
				],				
			];}
	if(!Yii::$app->user->isGuest){$items[]= [
				'label'=>'Nha khoa',
        'items'=>[
          ['label'=>'Thông tin nha khoa', 'url'=>'/nha-khoa/view?id=1'],
          ['label'=>'Quản lý bác sĩ', 'url'=>'/bac-si/index'],
        ],
				//'visible'=> Yii::$app->user->can('admin'),				
			];}
	if(!Yii::$app->user->isGuest){$items[]= [
				'label'=>'User',
				'url'=>['/user'],
				'visible'=> Yii::$app->user->can('admin'),
			];}
	// if(!Yii::$app->user->isGuest){$items[]=['label' => 'Contact', 'url' => ['/site/contact']];}	
	if(!Yii::$app->user->isGuest){$items[]= '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>';}else{$items[]=['label' => 'Login', 'url' => ['/site/login']];}		
	/**$items = [
			['label' => 'Home', 'url' => ['/site/index']],
			['label' => 'Danh sách điều trị', 'url' => ['/dieu-tri/index']],
			['label' => 'Quản lý Khách hàng', 'url' => ['/khach-hang/index']],
            ['label' => 'Thu Chi',
				'items'=>[
					['label' => 'Quản lý Thu Chi', 'url' => ['/thu-chi']],
					['label' => 'Loại Thu Chi', 'url' => ['/loai-thu-chi']],
				],				
			],
           	
			[
				'label'=>'Bác sĩ',
				'url'=>['/bac-si'],
				//'visible'=> Yii::$app->user->can('admin'),				
			],
			[
				'label'=>'User',
				'url'=>['/user'],
				'visible'=> Yii::$app->user->can('admin'),
			],
			['label' => 'Contact', 'url' => ['/site/contact']],		
		   Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ];**/
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy;<?= date('Y') ?></p>

        <p class="pull-right"><?php echo ""; ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
