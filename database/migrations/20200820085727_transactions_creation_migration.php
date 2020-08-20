<?php

use AgileCore\Models\Transaction;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class TransactionsCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Transaction::STORAGE)
            ->addColumn('reference', 'string', ['limit' => 32])
            ->addColumn('amount', 'float')
            ->addColumn('payment_mode_id', 'integer', ['null' => true])
            ->addColumn('payment_mode_method', 'string', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('status', 'string')
            ->addColumn('command_id', 'integer')
            ->addColumn('partner_response', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('transaction_type', 'string')
            ->save();
    }

}
