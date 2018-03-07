<?php
namespace app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Событие о обработке транзакции
 */
class OperationCompletedJob extends BaseObject implements JobInterface
{
    /**
     * ID обработанной транзакции
     *
     * @var integer
     */
    public $transaction_id;

    /**
     * Тип проведенной транзакции
     *
     * @var integer
     */
    public $type;

    /**
     * Расшифровка типа
     *
     * @var string
     */
    public $typeDescription;

    /**
     * Статус транзакции
     *
     * @var integer
     */
    public $status;

    /**
     * Расшифровка статуса транзакции
     *
     * @var string
     */
    public $statusDescription;

    /**
     * Сумма операции
     *
     * @var float
     */
    public $sum;

    /**
     * Последнее сообщение о ошибке
     *
     * @var string
     */
    public $lastError;

    /**
     * @param \yii\queue\Queue $queue
     */
    public function execute($queue)
    {

    }
}