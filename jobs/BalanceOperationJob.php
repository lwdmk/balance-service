<?php
namespace app\jobs;

use app\base\IncomeMessage;
use app\components\balance\services\TransactionsService;
use yii\base\Model;
use yii\base\UserException;
use yii\queue\Queue;

/**
 * Операция, полученная извне на изменение состояния счета
 */
class BalanceOperationJob extends Model implements \yii\queue\JobInterface
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
        /** @var IncomeMessage $incomeMessage */
        $incomeMessage = \Yii::createObject(IncomeMessage::class);
        /** @var TransactionsService $transactionsService */
        $transactionsService = \Yii::$container->get(TransactionsService::class);

        $incomeMessage->setAttributes($this->getAttributes());

        if ($incomeMessage->validate()) {
            if(null === \Yii::$app->internalQueue->push($transactionsService->createJobFromMessage($incomeMessage))) {
                throw new UserException('Failed to sent transaction job in internal queue');
            }
        } else {
            throw new UserException('Validation errors: ' . json_encode($incomeMessage->getErrors()));
        }
    }
}