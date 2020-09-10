<?php

use AgileCore\Models\Command;
use Phinx\Migration\AbstractMigration;

class CommandsHistoryCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(Command::getHistoryTable())
            ->addColumn('reference', 'string', ['limit' => '32'])
            ->addColumn('basket_id', 'integer')
            ->addColumn('order_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('status', 'string', ['limit' => '64', 'default' => Command::STATUS_DRAFT])
            ->addColumn('billing_address_id', 'integer')
            ->addColumn('shipping_address_id', 'integer')
            ->addColumn('invoice_pdf', 'string', ['null' => true])
            ->addColumn('transporter_id', 'integer')
            ->addColumn('shipping_fees', 'float')
            ->addColumn('tracking_number', 'string', ['null' => true])
            ->addColumn('history_date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();
    }

}
