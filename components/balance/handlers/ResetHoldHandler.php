<?php
namespace app\components\balance\handlers;

use app\components\balance\models\Accounts;
use app\components\balance\models\Transaction;
use yii\base\UserException;

/**
 * Обработчик операции возврата холда
 */
class ResetHoldHandler extends BaseHandler
{
    /**
     * Проводка операции возвращению средств из холда
     *
     * @param Transaction $transaction
     *
     * @return bool
     * @throws UserException
     */
    public function processOperation(Transaction & $transaction): bool
    {
        $parentTransaction = $this->transactionsService->getOneBy(['id' => $transaction->hold_transaction_id]);

        if(null === $parentTransaction){
            throw new UserException('Parent transaction with id = ' . $transaction->hold_transaction_id . ' not found');
        }

        /** @var Accounts $accountForIncome */
        $account = $this->balanceService->getOneBy(['id' => $parentTransaction->from_account]);

        $this->checkForHoldOperation($account, $parentTransaction);

        return $this->transactionsService->setStatus($transaction, Transaction::STATUS_PROCESSED)
            && $this->balanceService->changeBalance($account, ($parentTransaction->sum ))
            && $this->balanceService->changeHold($account, ($parentTransaction->sum * (-1)));
    }
}