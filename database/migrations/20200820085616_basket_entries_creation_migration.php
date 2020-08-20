<?php

use AgileCore\Models\BasketEntry;
use Phinx\Migration\AbstractMigration;

class BasketEntriesCreationMigration extends AbstractMigration {

    public function change() {
        $this->table(BasketEntry::STORAGE)
            ->addColumn('basket_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('quantity', 'integer')
            ->addColumn('entry_price', 'float', ['null' => true])
            ->addColumn('entry_discount', 'float', ['null' => true])
            ->save();
    }

}
