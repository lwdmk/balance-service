<?php
namespace app\components\balance\handlers;

use app\components\balance\models\Accounts;
use app\components\balance\models\Transaction;
use yii\base\UserException;

/**
 * Обработчик операции поступления денег на счет
 */
class IncomeHandler extends BaseHandler
{
    /**
     * Проводка операции зачисления на счет
     *
     * @param Transaction $transaction
     *
     * @return bool
     * @throws UserException
     */
    public function processOperation(Transaction &$transaction): bool
    {
        /** @var Accounts $accountForIncome */
        $accountForIncome = $this->balanceService->getOneBy(['id' => $transaction->to_account]);

        $this->checkForIncome($accountForIncome, $transaction);

        return $this->transactionsService->setStatus($transaction, Transaction::STATUS_PROCESSED)
            && $this->balanceService->changeBalance($accountForIncome, $transaction->sum);
    }
}