<?php
namespace app\components\balance\handlers;

use app\components\balance\models\Accounts;
use app\components\balance\models\Transaction;
use yii\base\UserException;

/**
 * Обработчик операции списания денегсо счета
 */
class WriteOffHandler extends BaseHandler
{
    /**
     * Проводка операции списания со счет
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
            && $this->balanceService->changeBalance($accountForWriteOff, ($transaction->sum * (-1)));
    }
}