<?php
namespace app\components\balance\handlers;

use app\components\balance\models\Accounts;
use app\components\balance\models\Transaction;
use app\components\balance\services\BalanceService;
use app\components\balance\services\TransactionsService;
use yii\base\BaseObject;
use yii\base\UserException;

/**
 * Базовый класс для обработком операций с транзакциями
 */
abstract class BaseHandler extends BaseObject
{
    /**
     * @var BalanceService
     */
    public $balanceService;

    /**
     * @var TransactionsService
     */
    public $transactionsService;

    /**
     * BaseHandler constructor.
     *
     * @param BalanceService $balanceService
     * @param TransactionsService $transactionsService
     * @param array $config
     */
    public function __construct(BalanceService $balanceService, TransactionsService $transactionsService, array $config = [])
    {
        $this->transactionsService = $transactionsService;
        $this->balanceService = $balanceService;
        parent::__construct($config);
    }

    /**
     * Процесс проведения операции
     *
     * @param Transaction $transaction
     *
     * @return bool
     * @throws UserException
     */
    public function process(Transaction &$transaction)
    {
        if($transaction->save()) {
            try {
                $dbTransaction = \Yii::$app->db->beginTransaction();
                if ($this->processOperation($transaction)) {
                    $dbTransaction->commit();
                    return true;
                } else {
                    $dbTransaction->rollBack();
                    return false;
                }
            } catch (\Exception $e) {
                $this->processException($e, $transaction);
                return false;
            }
        } else {
            throw new UserException('Transaction validation errors: ' . json_encode($transaction->getErrors()));
        }
    }

    /**
     * Процесс проведение конкретного типа операции
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public abstract function processOperation(Transaction &$transaction): bool;

    /**
     * @param \Exception $e
     * @param Transaction $transaction
     */
    protected function processException(\Exception $e, Transaction &$transaction)
    {
        $transaction->lastErrors = $e->getMessage();
        $transaction->update(false, ['lastErrors']);
        $this->transactionsService->setStatus($transaction, Transaction::STATUS_ERRORS);
    }

    /**
     * Проверка счета для списания
     *
     * @param Accounts $account
     * @param Transaction $transaction
     *
     * @throws UserException
     */
    protected function checkForWriteOff(Accounts $account, Transaction $transaction)
    {
        if(null === $account) {
            throw new UserException('Account with number ' . $transaction->from_account . ' not found');
        }
        if($transaction->sum > $account->balance) {
            throw new UserException('Not enough fund on account with number ' . $transaction->from_account);
        }
    }

    /**
     * Проверки счета для зачисления
     *
     * @param Accounts $account
     * @param Transaction $transaction
     *
     * @throws UserException
     */
    protected function checkForIncome(Accounts $account, Transaction $transaction)
    {
        if(null === $account) {
            throw new UserException('Account with number ' . $transaction->to_account . ' not found');
        }
    }

    /**
     * Проверки счета для операций с холдом
     *
     * @param Accounts $account
     * @param Transaction $parentTransaction
     *
     * @throws UserException
     */
    protected function checkForHoldOperation(Accounts $account, Transaction $parentTransaction)
    {
        if(null === $account) {
            throw new UserException('Account with number ' . $parentTransaction->from_account . ' not found');
        }

        if($account->hold < $parentTransaction->sum) {
            throw new UserException('Not enough funds at hold for transaction id = ' . $parentTransaction->id);
        }
    }
}