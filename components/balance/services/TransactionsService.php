<?php
namespace app\components\balance\services;

use app\base\IncomeMessage;
use app\components\balance\models\Transaction;
use app\jobs\TransactionJob;
use yii\base\BaseObject;

/**
 * Сервис для работы с сущностью транзакции
 */
class TransactionsService extends BaseObject
{
    /**
     * @param $condition
     *
     * @return Transaction
     */
    public function getOneBy($condition)
    {
        return Transaction::findOne($condition);
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getForProcess()
    {
        return Transaction::find()
            ->where(['status' => Transaction::STATUS_QUEUE])
            ->orderBy(['created_at' => 'ASC'])
            ->one();
    }

    /**
     * Обертка для процедуры обновления статуса
     *
     * @param Transaction $transaction
     * @param $newStatus
     *
     * @return bool
     */
    public function setStatus(Transaction &$transaction, $newStatus)
    {
        $transaction->status = $newStatus;
        return $transaction->save(false, ['status']);
    }

    /**
     * Создание транзакции на основе входящего сообщения
     *
     * @param IncomeMessage $message
     *
     * @return TransactionJob
     */
    public function createJobFromMessage(IncomeMessage &$message)
    {
        /** @var TransactionJob $transactionJob */
        $transactionJob = \Yii::createObject(TransactionJob::class);
        $transactionJob->type = $message->type;
        $transactionJob->from_account = $message->from_account;
        $transactionJob->to_account = $message->to_account;
        $transactionJob->sum = $message->sum;
        $transactionJob->hold_transaction_id = $message->hold_transaction_id;
        $transactionJob->comment = $message->comment;

        return $transactionJob;

    }
}