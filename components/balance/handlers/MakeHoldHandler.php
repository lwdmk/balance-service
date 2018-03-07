<?php
namespace app\components\balance\handlers;

use app\components\balance\models\Accounts;
use app\components\balance\models\Transaction;
use yii\base\UserException;

/**
 * Обработчик операции создания холда
 */
class MakeHoldHandler extends BaseHandler
{
    /**
     * Проводка операции перемещению средств со счета на в холд
     *
     * @param Transaction $transaction
     *
     * @return bool
     * @throws UserException
     */
    public function processOperation(Transaction &$transaction): bool
    {
        /** @var Accounts $accountForIncome */
        $accountForWriteOff = $this->balanceService->getOneBy(['id' => $transaction->from_account]);

        $this->checkForWriteOff($accountForWriteOff, $transaction);

        return $this->transactionsService->setStatus($transaction, Transaction::STATUS_PROCESSED)
            && $this->balanceService->changeBalance($accountForWriteOff, ($transaction->sum * (-1)))
            && $this->balanceService->changeHold($accountForWriteOff, ($transaction->sum));
    }
}