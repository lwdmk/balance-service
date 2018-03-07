<?php
namespace app\commands;

use app\components\balance\models\Transaction;
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
            'sum'                 => 50,
            'type'                => Transaction::TYPE_TRANSFER,
            'from_account'        => 2,
            'to_account'          => 1,
        ]));
    }
}