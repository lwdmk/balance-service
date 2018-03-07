<?php
namespace app\jobs;

use app\components\balance\handlers\BaseHandler;
use app\components\balance\handlers\CompleteHoldHandler;
use app\components\balance\handlers\IncomeHandler;
use app\components\balance\handlers\MakeHoldHandler;
use app\components\balance\handlers\ResetHoldHandler;
use app\components\balance\handlers\TransferHandler;
use app\components\balance\handlers\WriteOffHandler;
use app\components\balance\models\Transaction;
use yii\base\Model;
use yii\base\UserException;

/**
 * Задание на создание и обработку транзакции
 */
class TransactionJob extends Model implements \yii\queue\JobInterface
{
    /**
     * Тип необходимой операции
     *
     * @var integer
     */
    public $type;

    /**
     * ID аккаунта плательщика (при наличии)
     *
     * @var integer
     */
    public $from_account;

    /**
     * ID аккаунта получателя (при наличии)
     *
     * @var integer
     */
    public $to_account;

    /**
     * ID транзакции постановки в холд
     *
     * @var integer
     */
    public $hold_transaction_id;

    /**
     * Сумма операции
     *
     * @var float
     */
    public $sum;

    /**
     * Комментарий к операции
     *
     * @var string
     */
    public $comment;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $transaction = new Transaction();
        $transaction->setAttributes($this->getAttributes());

        if ($this->processTransactionByType($transaction)) {
            \Yii::$app->successQueue->push(new OperationCompletedJob([
                'transaction_id'    => $transaction->id,
                'type'              => $transaction->type,
                'typeDescription'   => $transaction->getTypeDescription(),
                'status'            => $transaction->status,
                'statusDescription' => $transaction->getStatusDescription(),
                'sum'               => $transaction->sum,
                'lastError'         => $transaction->lastErrors,
            ]));
        } else {
            throw new UserException($transaction->lastErrors);
        }
    }

    /**
     * Обработка транзакции соответсвующим обработчиком
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function processTransactionByType(Transaction &$transaction)
    {
        return ($this->getHandlerByType($transaction->type))->process($transaction);
    }

    /**
     * Создание класса обработчика транзакции
     *
     * @param $type
     *
     * @return BaseHandler|object
     * @throws UserException
     */
    protected function getHandlerByType($type)
    {
        switch ($type) {
            case Transaction::TYPE_INCOME : {
                return \Yii::createObject(IncomeHandler::class);
            }
            case Transaction::TYPE_WRITE_OFF : {
                return \Yii::createObject(WriteOffHandler::class);
            }
            case Transaction::TYPE_TRANSFER : {
                return \Yii::createObject(TransferHandler::class);
            }
            case Transaction::TYPE_HOLD : {
                return \Yii::createObject(MakeHoldHandler::class);
            }
            case Transaction::TYPE_HOLD_RESET : {
                return \Yii::createObject(ResetHoldHandler::class);
            }
            case Transaction::TYPE_HOLD_COMPLETED : {
                return \Yii::createObject(CompleteHoldHandler::class);
            }
            default : {
                throw new UserException('Handler for operation with type ' . $type . ' not found');
            }
        }
    }
}