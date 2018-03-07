<?php
namespace app\components\balance\models;

use yii\db\ActiveRecord;

/**
 * Модель Баланса
 *
 * @property int $id
 * @property int $user_id
 * @property float $balance
 * @property float $hold
 */
class Accounts extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['balance', 'hold'], 'double'],
            [['balance', 'hold'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'user_id' => 'User ID',
            'balance' => 'Balance',
            'hold'    => 'Hold',
        ];
    }
}