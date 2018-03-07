<?php

use yii\db\Migration;

/**
 * Class m180301_184229_init_tables
 */
class m180305_184229_init_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('accounts', [
            'id'      => $this->primaryKey(),
            'user_id' => $this->bigInteger()->unique()->unsigned(),
            'balance' => $this->decimal(12, 2),
            'hold'    => $this->decimal(12, 2)
        ]);

        $this->createTable('transactions', [
            'id'                  => $this->primaryKey(),
            'type'                => $this->tinyInteger()->unsigned(),
            'sum'                 => $this->decimal(12, 2),
            'from_account'        => $this->bigInteger()->null(),
            'to_account'          => $this->bigInteger()->null(),
            'hold_transaction_id' => $this->bigInteger()->null(),
            'status'              => $this->tinyInteger(),
            'comment'             => $this->text(),
            'lastErrors'          => $this->text(),
            'created_at'          => $this->integer(),
            'updated_at'          => $this->integer()
        ]);

        $this->addForeignKey('transactions_from_account_fk', 'transactions', 'from_account', 'accounts', 'id',
            'SET NULL', 'CASCADE');
        $this->addForeignKey('transactions_to_account_fk', 'transactions', 'to_account', 'accounts', 'id', 'SET NULL',
            'CASCADE');

        $this->addForeignKey('transactions_hold_transaction_id_fk', 'transactions', 'hold_transaction_id', 'transactions', 'id', 'RESTRICT',
            'CASCADE');

        $this->createIndex('account_user_id_index', 'accounts', 'user_id');
        $this->createIndex('transactions_status_created_at_index', 'transactions', ['created_at', 'status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('transactions_from_account_fk', 'transactions');
        $this->dropForeignKey('transactions_to_account_fk', 'transactions');
        $this->dropTable('transactions');
        $this->dropTable('accounts');
    }
}
