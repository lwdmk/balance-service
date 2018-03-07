<?php
namespace app\components\balance\services;

use app\components\balance\models\Accounts;
use yii\base\BaseObject;

/**
 * Сервис для работы с сущностью баланса
 */
class BalanceService extends BaseObject
{
    /**
     * @param $condition
     *
     * @return Accounts
     */
    public function getOneBy($condition)
    {
        return Accounts::findOne($condition);
    }

    /**
     * Обертка для изменения состояния счета
     *
     * @param Accounts $account
     * @param float $changeValue
     *
     * @return bool
     */
    public function changeBalance(Accounts &$account, float $changeValue)
    {
        $account->balance += $changeValue;
        return $account->update(false, ['balance']) > 0;
    }

    /**
     * Обертка для изменения состояния холда
     *
     * @param Accounts $account
     * @param float $changeValue
     *
     * @return bool
     */
    public function changeHold(Accounts &$account, float $changeValue)
    {
        $account->hold += $changeValue;
        return $account->update(false, ['hold']) > 0;
    }
}