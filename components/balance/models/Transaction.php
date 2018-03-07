<?php
namespace app\components\balance\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Модель Транзакции
 *
 * @property int $id
 * @property int $type
 * @property float $sum
 * @property int $from_account
 * @property int $to_account
 * @property int $status
 * @property string $comment
 * @property string $lastErrors
 * @property int $hold_transaction_id
 * @property int $created_at
 * @property int $updated_at
 */
class Transaction extends ActiveRecord
{
    /** Типы транзакций */
    const TYPE_INCOME = 1, TYPE_WRITE_OFF = 2, TYPE_TRANSFER = 3, TYPE_HOLD = 4, TYPE_HOLD_RESET = 5, TYPE_HOLD_COMPLETED = 6;

    /** Cтатусы операций */
    const STATUS_QUEUE = 1, STATUS_PROCESSED = 100, STATUS_ERRORS = 999;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sum', 'type'], 'required'],
            [['sum'], 'double'],
            [['from_account', 'to_account', 'hold_transaction_id'], 'integer'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_QUEUE],
            [['comment', 'lastErrors'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'sum'          => 'Sum',
            'from_account' => 'From Account',
            'to_account'   => 'To Account',
            'status'       => 'Status',
            'comment'      => 'Comment',
            'lastErrors'   => 'Last Errors',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At'
        ];
    }

    /**
     * Массив расшифровок для статусов
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_QUEUE     => 'In queue',
            self::STATUS_PROCESSED => 'Completed',
            self::STATUS_ERRORS    => 'Failed. Has errors'
        ];
    }

    /**
     * Массив расшифровок для типов
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_INCOME         => 'Income',
            self::TYPE_WRITE_OFF      => 'Write off',
            self::TYPE_TRANSFER       => 'Transfer',
            self::TYPE_HOLD           => 'Hold',
            self::TYPE_HOLD_RESET     => 'Hold reset',
            self::TYPE_HOLD_COMPLETED => 'Hold completed',
        ];
    }

    /**
     * Получение расшифровки текущего статуса операции
     *
     * @return mixed
     */
    public function getStatusDescription()
    {
        return ArrayHelper::getValue(self::getStatuses(), $this->status, 'Unknown status');
    }

    /**
     * Получение расшифровки текущего типа операции
     *
     * @return mixed
     */
    public function getTypeDescription()
    {
        return ArrayHelper::getValue(self::getTypes(), $this->type, 'Unknown type');
    }
}