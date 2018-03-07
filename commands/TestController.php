<?php
namespace app\commands;

use app\jobs\BalanceOperationJob;
use yii\console\Controller;

/**
 * Контролер для генерации тестовых данных
 */
class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    /**
     * Действие для проверки работы
     */
    public function actionIndex()
    {
        \Yii::$app->externalQueue->push(new BalanceOperationJob([
            'sum'        => 45.5,
            'type'       => 1,
            'to_account' => 1
        ]));
    }
}