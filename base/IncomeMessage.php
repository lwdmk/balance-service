<?php
namespace app\base;

use app\components\balance\models\Transaction;
use yii\base\Model;

/**
 * Обертка - валидатор для входящего запроса на операцию по счету
 */
class IncomeMessage extends Model
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
    public function rules() {
        return [
            [['sum', 'type'], 'required'],
            [
                ['from_account'],
                'required',
                'when' => function ($item) {
                    return in_array($item->type, [Transaction::TYPE_WRITE_OFF, Transaction::TYPE_TRANSFER, Transaction::TYPE_HOLD]);
                }
            ],
            [
                ['to_account'],
                'required',
                'when' => function ($item) {
                    return in_array($item->type, [Transaction::TYPE_INCOME, Transaction::TYPE_TRANSFER]);
                }
            ],
            [
                ['to_account', 'from_account'],
                'required',
                'when' => function ($item) {
                    return in_array($item->type, [Transaction::TYPE_TRANSFER]);
                }
            ],
            [['hold_transaction_id'], 'required', 'when' => function ($item) {
                return in_array($item->type, [Transaction::TYPE_HOLD_RESET, Transaction::TYPE_HOLD_COMPLETED]);
            }],
            [['sum'], 'double'],
            [['from_account', 'to_account', 'hold_transaction_id'], 'integer'],
            [['comment'], 'string'],
        ];
    }
}